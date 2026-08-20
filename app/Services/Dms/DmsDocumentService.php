<?php

namespace App\Services\Dms;

use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DmsDocumentService
{
    public function upload(UploadedFile $file, DmsFolder $folder, User $actor, ?string $name = null, ?int $classificationId = null): DmsDocument
    {
        $name = $name ?: $file->getClientOriginalName();
        $name = $this->uniqueSiblingName($name, $folder);

        return DB::transaction(function () use ($file, $folder, $actor, $name, $classificationId) {
            $document = DmsDocument::create([
                'folder_id' => $folder->id,
                'name' => $name,
                'classification_id' => $classificationId,
                'status' => 'draft',
                'current_version' => 1,
                'created_by' => $actor->id,
            ]);

            $document->addMedia($file)
                ->usingFileName($file->hashName())
                ->withCustomProperties(['version_number' => 1, 'uploaded_by' => $actor->id])
                ->toMediaCollection(DmsDocument::VERSIONS_COLLECTION, 'local');

            return $document->fresh();
        });
    }

    /**
     * @param  string|UploadedFile  $file  A path (e.g. a locally downloaded
     *                                     OnlyOffice save payload) or an UploadedFile.
     */
    public function addVersion(DmsDocument $document, string|UploadedFile $file, User $actor, ?string $note = null): DmsDocument
    {
        return DB::transaction(function () use ($document, $file, $actor, $note) {
            // Media relations are cached on the model instance the first
            // time they're accessed. A caller may hand us a $document that
            // already had its media relation loaded earlier in the request
            // (e.g. after a previous addVersion() call on the same
            // object) — refresh so latestVersionNumber() always reflects
            // the database, not a stale in-memory snapshot.
            $document = $document->fresh();
            $version = $document->latestVersionNumber() + 1;

            $document->addMedia($file)
                ->withCustomProperties(array_filter([
                    'version_number' => $version,
                    'uploaded_by' => $actor->id,
                    'note' => $note,
                ], fn ($value) => $value !== null))
                ->toMediaCollection(DmsDocument::VERSIONS_COLLECTION, 'local');

            $document->update(['current_version' => $version]);

            return $document->fresh();
        });
    }

    /**
     * Restoring never deletes or mutates history — it copies the target
     * version's content forward as a brand new version, matching the
     * "Immutable Records" rule.
     */
    public function restoreVersion(DmsDocument $document, int $versionNumber, User $actor): DmsDocument
    {
        $document = $document->fresh();
        $media = $document->versionMedia()->first(fn ($m) => (int) $m->getCustomProperty('version_number') === $versionNumber);

        if (! $media) {
            throw ValidationException::withMessages(['version' => 'That version does not exist.']);
        }

        return $this->addVersion($document, $media->getPath(), $actor, "Restored from version {$versionNumber}");
    }

    public function rename(DmsDocument $document, string $name): DmsDocument
    {
        $this->assertUniqueSiblingName($name, $document->folder, $document);
        $document->update(['name' => $name]);

        return $document->fresh();
    }

    public function move(DmsDocument $document, DmsFolder $target): DmsDocument
    {
        $this->assertUniqueSiblingName($document->name, $target, $document);
        $document->update(['folder_id' => $target->id]);

        return $document->fresh();
    }

    public function copy(DmsDocument $document, DmsFolder $target, User $actor): DmsDocument
    {
        return DB::transaction(function () use ($document, $target, $actor) {
            // See the note in addVersion() — avoid copying a stale cached
            // media relation.
            $document = $document->fresh();
            $name = $this->uniqueSiblingName($document->name, $target);

            $copy = DmsDocument::create([
                'folder_id' => $target->id,
                'name' => $name,
                'classification_id' => $document->classification_id,
                'status' => $document->status,
                'current_version' => $document->current_version,
                'created_by' => $actor->id,
            ]);

            foreach ($document->getMedia(DmsDocument::VERSIONS_COLLECTION) as $media) {
                $media->copy($copy, DmsDocument::VERSIONS_COLLECTION, 'local');
            }

            return $copy;
        });
    }

    public function lock(DmsDocument $document, User $actor): DmsDocument
    {
        if ($document->is_locked && $document->locked_by !== $actor->id) {
            throw ValidationException::withMessages(['document' => 'This document is already locked by another user.']);
        }

        $document->update(['is_locked' => true, 'locked_by' => $actor->id, 'locked_at' => now()]);

        return $document->fresh();
    }

    public function unlock(DmsDocument $document, User $actor): DmsDocument
    {
        if ($document->is_locked && $document->locked_by !== $actor->id && ! $actor->isAdmin()) {
            throw ValidationException::withMessages(['document' => 'Only the user who locked this document (or an admin) can unlock it.']);
        }

        $document->update(['is_locked' => false, 'locked_by' => null, 'locked_at' => null]);

        return $document->fresh();
    }

    public function delete(DmsDocument $document): void
    {
        $document->delete();
    }

    protected function assertUniqueSiblingName(string $name, DmsFolder $folder, ?DmsDocument $ignore = null): void
    {
        $query = DmsDocument::query()
            ->where('folder_id', $folder->id)
            ->where('name', $name);

        if ($ignore) {
            $query->whereKeyNot($ignore->id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'A document with this name already exists in this folder.']);
        }
    }

    protected function uniqueSiblingName(string $name, DmsFolder $folder): string
    {
        $candidate = $name;
        $suffix = 1;

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $base = $extension ? substr($name, 0, -(strlen($extension) + 1)) : $name;

        while (DmsDocument::query()->where('folder_id', $folder->id)->where('name', $candidate)->exists()) {
            $suffix++;
            $candidate = $extension ? "{$base} ({$suffix}).{$extension}" : "{$name} ({$suffix})";
        }

        return $candidate;
    }
}
