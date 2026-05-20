<?php

use App\Models\Accountant;
use App\Models\Administrator;
use App\Models\OpdDoctor;
use App\Models\Receptionist;
use App\Models\User;
use Filament\Panel;

test('user isAdmin returns true when user has admin profile', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);

    expect($user->isAdmin())->toBeTrue();
});

test('user isAdmin returns false when user has no admin profile', function () {
    $user = User::factory()->create();

    expect($user->isAdmin())->toBeFalse();
});

test('user isReceptionist returns true when user has receptionist profile', function () {
    $user = User::factory()->create();
    Receptionist::create(['user_id' => $user->id]);

    expect($user->isReceptionist())->toBeTrue();
});

test('user isAccountant returns true when user has accountant profile', function () {
    $user = User::factory()->create();
    Accountant::create(['user_id' => $user->id]);

    expect($user->isAccountant())->toBeTrue();
});

test('user isAnyDoctor returns true for opd doctor', function () {
    $user = User::factory()->create();
    OpdDoctor::create(['user_id' => $user->id]);

    expect($user->isAnyDoctor())->toBeTrue();
});

test('user hasAnyProfile returns false when no profiles exist', function () {
    $user = User::factory()->create();

    expect($user->hasAnyProfile())->toBeFalse();
});

test('user hasAnyProfile returns true when at least one profile exists', function () {
    $user = User::factory()->create();
    Receptionist::create(['user_id' => $user->id]);

    expect($user->hasAnyProfile())->toBeTrue();
});

test('user canAccessPanel returns true for admin panel when admin', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);

    $panel = mock(Panel::class);
    $panel->shouldReceive('getId')->andReturn('admin');

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('user canAccessPanel returns false for admin panel when not admin', function () {
    $user = User::factory()->create();

    $panel = mock(Panel::class);
    $panel->shouldReceive('getId')->andReturn('admin');

    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('user profiles attribute returns all profile relationships', function () {
    $user = User::factory()->create();

    expect($user->profiles)->toBeArray()
        ->and($user->profiles)->toHaveKeys([
            'admin', 'accountant', 'receptionist', 'opd_doctor',
            'ind_doctor', 'emergency_doctor', 'dentist',
            'ultrasound_doctor', 'xray_technician', 'nursing_staff', 'patient_manager',
        ]);
});
