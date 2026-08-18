<?php

use App\Models\DmsDocument;
use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Storage::fake('local');
    $this->documents = app(DmsDocumentService::class);
    $this->folders = app(DmsFolderService::class);
    $this->user = User::factory()->create();
    $this->other = User::factory()->create();
    $this->folder = $this->folders->create('Root', null, $this->user);
});

test('upload creates a document with version 1', function () {
    $file = UploadedFile::fake()->create('report.pdf', 10);

    $document = $this->documents->upload($file, $this->folder, $this->user);

    expect($document->name)->toBe('report.pdf')
        ->and($document->current_version)->toBe(1)
        ->and($document->versionMedia())->toHaveCount(1);
});

test('addVersion increments the version and updates current_version', function () {
    $file = UploadedFile::fake()->create('report.pdf', 10);
    $document = $this->documents->upload($file, $this->folder, $this->user);

    $file2 = UploadedFile::fake()->create('report-v2.pdf', 10);
    $updated = $this->documents->addVersion($document, $file2, $this->user);

    expect($updated->current_version)->toBe(2)
        ->and($updated->versionMedia())->toHaveCount(2);
});

test('restoreVersion creates a new version rather than mutating history', function () {
    $file1 = UploadedFile::fake()->create('v1.pdf', 10);
    $document = $this->documents->upload($file1, $this->folder, $this->user);

    $file2 = UploadedFile::fake()->create('v2.pdf', 10);
    $this->documents->addVersion($document, $file2, $this->user);

    $restored = $this->documents->restoreVersion($document, 1, $this->user);

    expect($restored->current_version)->toBe(3)
        ->and($restored->versionMedia())->toHaveCount(3);
});

test('lock blocks another user from editing, but not the locking user', function () {
    $file = UploadedFile::fake()->create('report.pdf', 10);
    $document = $this->documents->upload($file, $this->folder, $this->user);

    $this->documents->lock($document, $this->user);

    expect(fn () => $this->documents->lock($document->fresh(), $this->other))
        ->toThrow(ValidationException::class);

    // Locking user can re-lock/continue without issue.
    $this->documents->lock($document->fresh(), $this->user);
});

test('unlock is rejected for a user who did not lock it, unless admin', function () {
    $file = UploadedFile::fake()->create('report.pdf', 10);
    $document = $this->documents->upload($file, $this->folder, $this->user);
    $this->documents->lock($document, $this->user);

    expect(fn () => $this->documents->unlock($document->fresh(), $this->other))
        ->toThrow(ValidationException::class);
});

test('rename rejects a duplicate sibling name', function () {
    $a = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->user);
    $b = $this->documents->upload(UploadedFile::fake()->create('b.pdf', 5), $this->folder, $this->user);

    expect(fn () => $this->documents->rename($b, 'a.pdf'))->toThrow(ValidationException::class);
});

test('copy duplicates the document and its version history into the target folder', function () {
    $target = $this->folders->create('Target', null, $this->user);
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->user);
    $this->documents->addVersion($document, UploadedFile::fake()->create('a-v2.pdf', 5), $this->user);

    $copy = $this->documents->copy($document, $target, $this->user);

    expect($copy->id)->not->toBe($document->id)
        ->and($copy->folder_id)->toBe($target->id)
        ->and($copy->versionMedia())->toHaveCount(2)
        ->and(DmsDocument::query()->count())->toBe(2);
});
