<?php

use App\Models\Administrator;
use App\Models\ServiceRecestation;
use App\Models\User;

test('service recestation admin page requires authentication', function () {
    $this->get('/admin/service-recestations')->assertRedirect();
});

test('admin user can access service recestation list page', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'full']);

    $this->actingAs($user);
    $this->get('/admin/service-recestations')->assertSuccessful();
});

test('service recestation factory creates valid record', function () {
    $recestation = ServiceRecestation::factory()->create();

    expect($recestation)->toBeInstanceOf(ServiceRecestation::class)
        ->and($recestation->name)->not->toBeNull()
        ->and($recestation->slug)->not->toBeNull()
        ->and($recestation->department)->not->toBeNull()
        ->and($recestation->creator)->not->toBeNull();
});

test('service recestation belongs to department', function () {
    $recestation = ServiceRecestation::factory()->create();

    expect($recestation->department)->not->toBeNull()
        ->and($recestation->department->id)->toBe($recestation->service_department_id);
});

test('service recestation belongs to creator', function () {
    $recestation = ServiceRecestation::factory()->create();

    expect($recestation->creator)->not->toBeNull()
        ->and($recestation->creator->id)->toBe($recestation->created_by);
});
