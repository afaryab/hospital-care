<?php

use App\Filament\Admin\Pages\DocumentManager;
use App\Models\Administrator;
use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
    $this->admin = User::factory()->create();
    Administrator::create(['user_id' => $this->admin->id, 'authority' => 'administrator']);
    $this->actingAs($this->admin);
});

test('the page renders for an admin and provisions the system roots', function () {
    Livewire::test(DocumentManager::class)
        ->assertSuccessful()
        ->assertSee('Patients')
        ->assertSee('Doctors');
});

test('a non-admin cannot access the page', function () {
    $receptionist = User::factory()->create();
    $this->actingAs($receptionist);

    expect(DocumentManager::canAccess())->toBeFalse();
});

test('createFolder creates a folder at the current level', function () {
    Livewire::test(DocumentManager::class)
        ->set('newFolderName', 'Reports')
        ->call('createFolder')
        ->assertSee('Reports');

    expect(DmsFolder::query()->where('name', 'Reports')->exists())->toBeTrue();
});

test('createFolder rejects a duplicate sibling name with a notification, not a crash', function () {
    Livewire::test(DocumentManager::class)
        ->set('newFolderName', 'Reports')
        ->call('createFolder')
        ->set('newFolderName', 'Reports')
        ->call('createFolder')
        ->assertSuccessful();

    expect(DmsFolder::query()->where('name', 'Reports')->count())->toBe(1);
});

test('openFolder navigates into a folder and back out via breadcrumbs', function () {
    $component = Livewire::test(DocumentManager::class)
        ->set('newFolderName', 'Reports')
        ->call('createFolder');

    $folder = DmsFolder::query()->where('name', 'Reports')->firstOrFail();

    $component->call('openFolder', $folder->id)
        ->assertSet('currentFolderId', $folder->id)
        ->call('openFolder', null)
        ->assertSet('currentFolderId', null);
});

test('uploadDocument uploads into the currently open folder', function () {
    $component = Livewire::test(DocumentManager::class)
        ->set('newFolderName', 'Reports')
        ->call('createFolder');

    $folder = DmsFolder::query()->where('name', 'Reports')->firstOrFail();

    $component->call('openFolder', $folder->id)
        ->set('uploadFile', UploadedFile::fake()->create('a.pdf', 5))
        ->call('uploadDocument')
        ->assertSee('a.pdf');
});

test('renaming a folder updates its name', function () {
    $component = Livewire::test(DocumentManager::class)
        ->set('newFolderName', 'Reports')
        ->call('createFolder');

    $folder = DmsFolder::query()->where('name', 'Reports')->firstOrFail();

    $component->call('startRenameFolder', $folder->id)
        ->set('renameValue', 'Reports 2026')
        ->call('confirmRename')
        ->assertSee('Reports 2026');
});

test('deleting a system folder is rejected', function () {
    $component = Livewire::test(DocumentManager::class);
    $root = DmsFolder::query()->where('name', 'Patients')->firstOrFail();

    $component->call('deleteFolder', $root->id)->assertSuccessful();

    expect(DmsFolder::query()->find($root->id))->not->toBeNull();
});
