<?php

use App\Models\Administrator;
use App\Models\DmsFolder;
use App\Models\User;
use App\Services\Dms\DmsFolderService;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);

    $this->folders = app(DmsFolderService::class);
});

test('a folder can be created at the root', function () {
    post(route('dms.folders.store'), ['name' => 'Reports'])
        ->assertRedirect();

    expect(DmsFolder::query()->where('name', 'Reports')->whereNull('parent_id')->exists())->toBeTrue();
});

test('a folder can be created inside a parent', function () {
    $parent = $this->folders->create('Reports', null, $this->admin);

    post(route('dms.folders.store'), ['name' => '2026', 'parent_uuid' => $parent->uuid])
        ->assertRedirect();

    expect(DmsFolder::query()->where('name', '2026')->where('parent_id', $parent->id)->exists())->toBeTrue();
});

test('creating a duplicate sibling folder is rejected with a validation error', function () {
    $parent = $this->folders->create('Reports', null, $this->admin);
    $this->folders->create('2026', $parent, $this->admin);

    post(route('dms.folders.store'), ['name' => '2026', 'parent_uuid' => $parent->uuid])
        ->assertSessionHasErrors();
});

test('a folder can be renamed', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);

    patch(route('dms.folders.update', $folder), ['name' => 'Annual Reports'])
        ->assertRedirect();

    expect($folder->fresh()->name)->toBe('Annual Reports');
});

test('a system folder cannot be renamed', function () {
    $folder = DmsFolder::factory()->system()->create(['created_by' => $this->admin->id]);

    patch(route('dms.folders.update', $folder), ['name' => 'Renamed'])
        ->assertSessionHasErrors();
});

test('an empty folder can be deleted', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);

    delete(route('dms.folders.destroy', $folder))->assertRedirect();

    expect(DmsFolder::query()->find($folder->id))->toBeNull();
});

test('a non-empty folder cannot be deleted', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);
    $this->folders->create('2026', $folder, $this->admin);

    delete(route('dms.folders.destroy', $folder))->assertSessionHasErrors();

    expect(DmsFolder::query()->find($folder->id))->not->toBeNull();
});

test('a folder can be moved into another folder', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);
    $target = $this->folders->create('Archive', null, $this->admin);

    post(route('dms.folders.move', $folder), ['target_uuid' => $target->uuid])
        ->assertRedirect();

    expect($folder->fresh()->parent_id)->toBe($target->id);
});

test('a folder cannot be moved into its own descendant', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);
    $child = $this->folders->create('2026', $folder, $this->admin);

    post(route('dms.folders.move', $folder), ['target_uuid' => $child->uuid])
        ->assertSessionHasErrors();
});

test('a folder can be copied into another folder', function () {
    $folder = $this->folders->create('Reports', null, $this->admin);
    $target = $this->folders->create('Archive', null, $this->admin);

    post(route('dms.folders.copy', $folder), ['target_uuid' => $target->uuid])
        ->assertRedirect();

    expect(DmsFolder::query()->where('parent_id', $target->id)->where('name', 'Reports')->exists())->toBeTrue();
    expect(DmsFolder::query()->find($folder->id))->not->toBeNull();
});
