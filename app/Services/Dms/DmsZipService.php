<?php

namespace App\Services\Dms;

use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class DmsZipService
{
    public function __construct(protected DmsDocumentService $documentService) {}

    /**
     * @return array{bytes: int, files: int}
     */
    public function folderSizeAndCount(DmsFolder $folder): array
    {
        $documents = $this->collectDocuments($folder);

        $bytes = $documents->sum(fn (DmsDocument $document) => $document->currentVersionMedia()?->size ?? 0);

        return ['bytes' => (int) $bytes, 'files' => $documents->count()];
    }

    public function fitsSyncThreshold(DmsFolder $folder): bool
    {
        $stats = $this->folderSizeAndCount($folder);

        return $stats['bytes'] <= config('dms.zip.sync_max_bytes')
            && $stats['files'] <= config('dms.zip.sync_max_files');
    }

    /**
     * Streams a zip to a temp file on disk (never buffered fully in
     * memory) mirroring the folder's tree structure, and returns its
     * absolute path. Caller is responsible for deleting it after use.
     */
    public function buildZipForFolder(DmsFolder $folder): string
    {
        $tmpDir = $this->tmpDir();
        $zipPath = $tmpDir.'/'.Str::slug($folder->name).'-'.$folder->uuid.'.zip';

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create zip archive for download.');
        }

        foreach ($this->collectDocuments($folder) as $document) {
            $media = $document->currentVersionMedia();
            if (! $media) {
                continue;
            }

            $relativeFolder = $this->relativePath($folder, $document->folder);
            $entryName = trim($relativeFolder.'/'.$document->name, '/');
            $zip->addFile($media->getPath(), $entryName);
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * The zip-bomb defense: every entry is inspected with statIndex() (no
     * extraction) before anything is written to disk. Rejects oversized
     * entry counts, oversized total uncompressed size, suspicious
     * compression ratios (the classic bomb signature), zip-slip path
     * traversal, and nested archives.
     *
     * @return array<int, DmsDocument>
     */
    public function extractZipSafely(UploadedFile $file, DmsFolder $target, User $actor): array
    {
        $zip = new ZipArchive;
        if ($zip->open($file->getRealPath()) !== true) {
            throw ValidationException::withMessages(['file' => 'Unable to read this zip file.']);
        }

        try {
            $entryNames = $this->assertZipIsSafe($zip);

            $tmpDir = $this->tmpDir().'/extract-'.Str::uuid();
            if (! is_dir($tmpDir) && ! mkdir($tmpDir, 0755, true) && ! is_dir($tmpDir)) {
                throw new \RuntimeException("Unable to create extraction directory: {$tmpDir}");
            }

            $zip->extractTo($tmpDir);
            $zip->close();

            $documents = [];
            foreach ($entryNames as $entry) {
                $absolute = $tmpDir.'/'.$entry;

                if (! is_file($absolute)) {
                    continue;
                }

                $uploaded = new UploadedFile($absolute, basename($entry), null, null, true);
                $documents[] = $this->documentService->upload($uploaded, $target, $actor);
            }

            File::deleteDirectory($tmpDir);

            return $documents;
        } finally {
            if ($zip->filename) {
                @$zip->close();
            }
        }
    }

    /**
     * @return array<int, string> the safe, non-directory entry names
     */
    protected function assertZipIsSafe(ZipArchive $zip): array
    {
        $maxEntries = (int) config('dms.zip.max_entries');
        $maxUncompressed = (int) config('dms.zip.max_uncompressed_bytes');
        $maxRatio = (int) config('dms.zip.max_compression_ratio');

        if ($zip->numFiles > $maxEntries) {
            throw ValidationException::withMessages(['file' => "This zip contains too many files (max {$maxEntries})."]);
        }

        $totalUncompressed = 0;
        $entryNames = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat === false) {
                throw ValidationException::withMessages(['file' => 'This zip file appears to be corrupt.']);
            }

            $name = $stat['name'];

            if ($this->isUnsafePath($name)) {
                throw ValidationException::withMessages(['file' => "This zip contains an unsafe file path: {$name}."]);
            }

            if (Str::endsWith(strtolower($name), '.zip')) {
                throw ValidationException::withMessages(['file' => 'Nested zip archives are not allowed.']);
            }

            $uncompressed = (int) $stat['size'];
            $compressed = max(1, (int) $stat['comp_size']);
            $ratio = $uncompressed / $compressed;

            if ($ratio > $maxRatio) {
                throw ValidationException::withMessages(['file' => "This zip file was rejected as a suspected zip bomb (entry \"{$name}\" has a compression ratio of {$ratio}x)."]);
            }

            $totalUncompressed += $uncompressed;

            if ($totalUncompressed > $maxUncompressed) {
                throw ValidationException::withMessages(['file' => 'This zip file would expand beyond the allowed size limit.']);
            }

            if (! Str::endsWith($name, '/')) {
                $entryNames[] = $name;
            }
        }

        return $entryNames;
    }

    protected function isUnsafePath(string $name): bool
    {
        if (str_starts_with($name, '/') || preg_match('#^[A-Za-z]:[\\\\/]#', $name)) {
            return true;
        }

        foreach (explode('/', str_replace('\\', '/', $name)) as $segment) {
            if ($segment === '..') {
                return true;
            }
        }

        return false;
    }

    /**
     * All documents inside $folder and every descendant, found via the
     * materialized path prefix.
     *
     * @return Collection<int, DmsDocument>
     */
    protected function collectDocuments(DmsFolder $folder): Collection
    {
        $folderIds = $folder->descendantsQuery()->pluck('id')->push($folder->id);

        return DmsDocument::query()->whereIn('folder_id', $folderIds)->get();
    }

    protected function relativePath(DmsFolder $root, DmsFolder $folder): string
    {
        if ($folder->id === $root->id) {
            return '';
        }

        $names = [];
        $current = $folder;

        while ($current !== null && $current->id !== $root->id) {
            array_unshift($names, $current->name);
            $current = $current->parent;
        }

        return implode('/', $names);
    }

    protected function tmpDir(): string
    {
        $dir = Storage::disk('local')->path(config('dms.tmp_path'));

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException("Unable to create DMS tmp directory: {$dir}");
        }

        return $dir;
    }
}
