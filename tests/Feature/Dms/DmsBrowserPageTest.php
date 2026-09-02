<?php

use App\Models\Administrator;
use App\Models\DmsFolder;
use App\Models\User;
use App\Services\Dms\DmsFolderService;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);
});

test('the root of the drive lists top-level folders and provisions the system roots', function () {
    get(route('dms.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dms/index')
            ->where('folder', null)
            ->has('breadcrumbs', 0)
            ->has('folders', 2) // Patients + Doctors system roots
        );

    expect(DmsFolder::query()->where('name', 'Patients')->where('is_system', true)->exists())->toBeTrue();
    expect(DmsFolder::query()->where('name', 'Doctors')->where('is_system', true)->exists())->toBeTrue();
});

test('opening a nested folder renders its children and breadcrumb trail', function () {
    $folders = app(DmsFolderService::class);
    $root = $folders->create('Reports', null, $this->admin);
    $child = $folders->create('2026', $root, $this->admin);

    get(route('dms.index', $child))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dms/index')
            ->where('folder.uuid', $child->uuid)
            ->has('breadcrumbs', 2)
            ->where('breadcrumbs.0.uuid', $root->uuid)
            ->where('breadcrumbs.1.uuid', $child->uuid)
        );
});

test('a non-admin is forbidden from the drive', function () {
    actingAs(User::factory()->create());

    get(route('dms.index'))->assertForbidden();
});
