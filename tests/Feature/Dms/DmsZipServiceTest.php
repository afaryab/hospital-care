<?php

use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use App\Services\Dms\DmsZipService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Storage::fake('local');
    config(['dms.tmp_path' => 'dms-tmp-test']);
    $this->zip = app(DmsZipService::class);
    $this->folders = app(DmsFolderService::class);
    $this->documents = app(DmsDocumentService::class);
    $this->user = User::factory()->create();
});

function makeZipFixture(array $entries, ?callable $writer = null): string
{
    $path = tempnam(sys_get_temp_dir(), 'dmszip').'.zip';
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    if ($writer) {
        $writer($zip);
    } else {
        foreach ($entries as $name => $contents) {
            $zip->addFromString($name, $contents);
        }
    }

    $zip->close();

    return $path;
}

test('buildZipForFolder mirrors the folder tree and includes each document current version', function () {
    $root = $this->folders->create('Root', null, $this->user);
    $sub = $this->folders->create('Sub', $root, $this->user);
    $this->documents->upload(UploadedFile::fake()->create('a.txt', 1), $root, $this->user);
    $this->documents->upload(UploadedFile::fake()->create('b.txt', 1), $sub, $this->user);

    $zipPath = $this->zip->buildZipForFolder($root);

    expect(file_exists($zipPath))->toBeTrue();

    $archive = new ZipArchive;
    $archive->open($zipPath);
    $names = [];
    for ($i = 0; $i < $archive->numFiles; $i++) {
        $names[] = $archive->statIndex($i)['name'];
    }
    $archive->close();
    @unlink($zipPath);

    expect($names)->toContain('a.txt')
        ->and($names)->toContain('Sub/b.txt');
});

test('extractZipSafely rejects a zip with too many entries', function () {
    config(['dms.zip.max_entries' => 2]);
    $target = $this->folders->create('Target', null, $this->user);

    $path = makeZipFixture(['a.txt' => 'a', 'b.txt' => 'b', 'c.txt' => 'c']);
    $upload = new UploadedFile($path, 'many.zip', 'application/zip', null, true);

    expect(fn () => $this->zip->extractZipSafely($upload, $target, $this->user))
        ->toThrow(ValidationException::class);

    @unlink($path);
});

test('extractZipSafely rejects a suspected zip bomb via compression ratio', function () {
    config(['dms.zip.max_compression_ratio' => 10]);
    $target = $this->folders->create('Target', null, $this->user);

    // Highly compressible content (a huge run of the same byte) mimics a
    // bomb's giveaway signature: tiny compressed size, huge uncompressed size.
    $path = makeZipFixture([], function (ZipArchive $zip) {
        $zip->addFromString('bomb.txt', str_repeat('0', 5 * 1024 * 1024));
        $zip->setCompressionName('bomb.txt', ZipArchive::CM_DEFLATE, 9);
    });
    $upload = new UploadedFile($path, 'bomb.zip', 'application/zip', null, true);

    expect(fn () => $this->zip->extractZipSafely($upload, $target, $this->user))
        ->toThrow(ValidationException::class);

    @unlink($path);
});

test('extractZipSafely rejects zip-slip path traversal entries', function () {
    $target = $this->folders->create('Target', null, $this->user);

    $path = makeZipFixture(['../../etc/passwd' => 'evil']);
    $upload = new UploadedFile($path, 'slip.zip', 'application/zip', null, true);

    expect(fn () => $this->zip->extractZipSafely($upload, $target, $this->user))
        ->toThrow(ValidationException::class);

    @unlink($path);
});

test('extractZipSafely rejects nested zip archives', function () {
    $target = $this->folders->create('Target', null, $this->user);

    $path = makeZipFixture(['inner.zip' => 'not really a zip but treated as one by extension']);
    $upload = new UploadedFile($path, 'nested.zip', 'application/zip', null, true);

    expect(fn () => $this->zip->extractZipSafely($upload, $target, $this->user))
        ->toThrow(ValidationException::class);

    @unlink($path);
});

test('extractZipSafely imports every entry of a safe zip as a document', function () {
    $target = $this->folders->create('Target', null, $this->user);

    $path = makeZipFixture(['a.txt' => 'hello', 'b.txt' => 'world']);
    $upload = new UploadedFile($path, 'safe.zip', 'application/zip', null, true);

    $documents = $this->zip->extractZipSafely($upload, $target, $this->user);

    expect($documents)->toHaveCount(2)
        ->and($target->documents()->count())->toBe(2);

    @unlink($path);
});
