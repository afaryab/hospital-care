<?php

use App\Models\DmsFolder;
use App\Models\User;
use App\Services\Dms\DmsFolderService;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = app(DmsFolderService::class);
    $this->user = User::factory()->create();
});

test('creates a root folder with materialized path /', function () {
    $folder = $this->service->create('Reports', null, $this->user);

    expect($folder->path)->toBe('/')
        ->and($folder->parent_id)->toBeNull()
        ->and($folder->created_by)->toBe($this->user->id);
});

test('creates a nested folder with a path built from its parent', function () {
    $parent = $this->service->create('Reports', null, $this->user);
    $child = $this->service->create('2026', $parent, $this->user);

    expect($child->path)->toBe('/'.$parent->id.'/');
});

test('rejects duplicate sibling names', function () {
    $this->service->create('Reports', null, $this->user);

    $this->service->create('Reports', null, $this->user);
})->throws(ValidationException::class);

test('rename updates the name and rejects a duplicate sibling name', function () {
    $parent = $this->service->create('Root', null, $this->user);
    $a = $this->service->create('A', $parent, $this->user);
    $b = $this->service->create('B', $parent, $this->user);

    $renamed = $this->service->rename($a, 'A2');
    expect($renamed->name)->toBe('A2');

    expect(fn () => $this->service->rename($b, 'A2'))->toThrow(ValidationException::class);
});

test('move updates the folder and all descendant paths', function () {
    $root = $this->service->create('Root', null, $this->user);
    $branchA = $this->service->create('BranchA', $root, $this->user);
    $branchB = $this->service->create('BranchB', $root, $this->user);
    $leaf = $this->service->create('Leaf', $branchA, $this->user);

    $this->service->move($branchA, $branchB);

    $branchA->refresh();
    $leaf->refresh();

    expect($branchA->parent_id)->toBe($branchB->id)
        ->and($branchA->path)->toBe($branchB->path.$branchB->id.'/')
        ->and($leaf->path)->toBe($branchA->path.$branchA->id.'/');
});

test('move rejects moving a folder into its own descendant', function () {
    $root = $this->service->create('Root', null, $this->user);
    $child = $this->service->create('Child', $root, $this->user);

    $this->service->move($root, $child);
})->throws(ValidationException::class);

test('copy deep-copies a folder subtree', function () {
    $root = $this->service->create('Root', null, $this->user);
    $branch = $this->service->create('Branch', $root, $this->user);
    $this->service->create('Leaf', $branch, $this->user);
    $target = $this->service->create('Target', null, $this->user);

    $copy = $this->service->copy($branch, $target, $this->user);

    expect($copy->id)->not->toBe($branch->id)
        ->and($copy->name)->toBe('Branch')
        ->and($copy->children()->count())->toBe(1)
        ->and($copy->children()->first()->name)->toBe('Leaf');

    // Original subtree is untouched.
    expect($branch->children()->count())->toBe(1);
});

test('copy into a folder that already has a same-named child auto-suffixes the name', function () {
    $root = $this->service->create('Root', null, $this->user);
    $branch = $this->service->create('Branch', $root, $this->user);
    $target = $this->service->create('Target', null, $this->user);
    $this->service->create('Branch', $target, $this->user);

    $copy = $this->service->copy($branch, $target, $this->user);

    expect($copy->name)->toBe('Branch (2)');
});

test('delete removes an empty folder', function () {
    $folder = $this->service->create('Empty', null, $this->user);

    $this->service->delete($folder);

    expect(DmsFolder::query()->find($folder->id))->toBeNull();
});

test('delete blocks a non-empty folder', function () {
    $folder = $this->service->create('NotEmpty', null, $this->user);
    $this->service->create('Child', $folder, $this->user);

    $this->service->delete($folder);
})->throws(ValidationException::class);

test('delete blocks a system folder', function () {
    $folder = DmsFolder::factory()->system()->create(['created_by' => $this->user->id]);

    $this->service->delete($folder);
})->throws(ValidationException::class);
