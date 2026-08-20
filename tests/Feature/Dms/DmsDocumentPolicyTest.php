<?php

use App\Models\Administrator;
use App\Models\DmsShare;
use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->folders = app(DmsFolderService::class);
    $this->documents = app(DmsDocumentService::class);
    $this->owner = User::factory()->create();
    $this->stranger = User::factory()->create();
    $this->folder = $this->folders->create('Root', null, $this->owner);
});

test('admin can view any document', function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);

    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);

    expect(Gate::forUser($admin)->allows('view', $document))->toBeTrue();
});

test('the creator can view their own document', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);

    expect(Gate::forUser($this->owner)->allows('view', $document))->toBeTrue();
});

test('a document inherits view access from its containing folder', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);

    DmsShare::factory()->create([
        'folder_id' => $this->folder->id,
        'grantee_type' => DmsShare::GRANTEE_USER,
        'grantee_value' => (string) $this->stranger->id,
        'ability' => 'view',
        'created_by' => $this->owner->id,
    ]);

    expect(Gate::forUser($this->stranger)->allows('view', $document))->toBeTrue();
});

test('a stranger cannot view a document with no folder access', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);

    expect(Gate::forUser($this->stranger)->allows('view', $document))->toBeFalse();
});

test('a direct document share grants access even without folder access', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);

    DmsShare::factory()->create([
        'document_id' => $document->id,
        'grantee_type' => DmsShare::GRANTEE_USER,
        'grantee_value' => (string) $this->stranger->id,
        'ability' => 'view',
        'created_by' => $this->owner->id,
    ]);

    expect(Gate::forUser($this->stranger)->allows('view', $document))->toBeTrue();
});

test('a locked document cannot be updated by anyone but the locker or an admin', function () {
    $document = $this->documents->upload(UploadedFile::fake()->create('a.pdf', 1), $this->folder, $this->owner);
    $this->documents->lock($document, $this->owner);

    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);

    expect(Gate::forUser($this->owner)->allows('update', $document->fresh()))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $document->fresh()))->toBeTrue();
});
