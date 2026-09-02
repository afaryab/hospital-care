<?php

use App\Models\Administrator;
use App\Models\DmsDocument;
use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('local');

    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);

    $this->folders = app(DmsFolderService::class);
    $this->documents = app(DmsDocumentService::class);
    $this->folder = $this->folders->create('Reports', null, $this->admin);
});

test('a document can be uploaded into a folder', function () {
    post(route('dms.documents.store'), [
        'file' => UploadedFile::fake()->create('summary.pdf', 5),
        'folder_uuid' => $this->folder->uuid,
    ])->assertRedirect();

    expect(DmsDocument::query()->where('folder_id', $this->folder->id)->where('name', 'summary.pdf')->exists())->toBeTrue();
});

test('a document can be renamed', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);

    patch(route('dms.documents.update', $document), ['name' => 'renamed.pdf'])->assertRedirect();

    expect($document->fresh()->name)->toBe('renamed.pdf');
});

test('a document can be deleted', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);

    delete(route('dms.documents.destroy', $document))->assertRedirect();

    expect(DmsDocument::query()->find($document->id))->toBeNull();
});

test('a document can be moved into another folder', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);
    $target = $this->folders->create('Archive', null, $this->admin);

    post(route('dms.documents.move', $document), ['target_uuid' => $target->uuid])->assertRedirect();

    expect($document->fresh()->folder_id)->toBe($target->id);
});

test('a document can be copied into another folder', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);
    $target = $this->folders->create('Archive', null, $this->admin);

    post(route('dms.documents.copy', $document), ['target_uuid' => $target->uuid])->assertRedirect();

    expect(DmsDocument::query()->where('folder_id', $target->id)->where('name', 'a.pdf')->exists())->toBeTrue();
    expect(DmsDocument::query()->find($document->id))->not->toBeNull();
});

test('a document can be locked and then unlocked by the same user', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);

    post(route('dms.documents.lock', $document))->assertRedirect();
    expect($document->fresh()->is_locked)->toBeTrue();

    post(route('dms.documents.unlock', $document))->assertRedirect();
    expect($document->fresh()->is_locked)->toBeFalse();
});

test('locking a document already locked by another user is rejected', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 5), $this->folder, $this->admin);
    $other = User::factory()->create();
    Administrator::factory()->create(['user_id' => $other->id]);
    $this->documents->lock($document, $other);

    post(route('dms.documents.lock', $document))->assertSessionHasErrors();
});
