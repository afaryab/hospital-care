<?php

use App\Models\Consent;
use App\Models\EmergencyDoctor;
use App\Models\NursingStaff;
use App\Models\Receptionist;
use App\Models\User;

test('admin can view, view any, and create consents', function () {
    $consent = Consent::factory()->create();

    expect(adminUser()->can('view', $consent))->toBeTrue()
        ->and(adminUser()->can('viewAny', Consent::class))->toBeTrue()
        ->and(adminUser()->can('create', Consent::class))->toBeTrue();
});

test('a receptionist can view and create consents', function () {
    $user = User::factory()->create();
    Receptionist::create(['user_id' => $user->id]);
    $consent = Consent::factory()->create();

    expect($user->can('view', $consent))->toBeTrue()
        ->and($user->can('create', Consent::class))->toBeTrue();
});

test('nursing staff can view and create consents', function () {
    $user = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $user->id]);
    $consent = Consent::factory()->create();

    expect($user->can('view', $consent))->toBeTrue()
        ->and($user->can('create', Consent::class))->toBeTrue();
});

test('any doctor can view and create consents, not scoped to their own patients', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $consent = Consent::factory()->create();

    expect($doctor->can('view', $consent))->toBeTrue()
        ->and($doctor->can('create', Consent::class))->toBeTrue();
});

test('a user with no clinical or front-desk profile cannot view or create consents', function () {
    $user = User::factory()->create();
    $consent = Consent::factory()->create();

    expect($user->can('view', $consent))->toBeFalse()
        ->and($user->can('create', Consent::class))->toBeFalse();
});

test('consent records cannot be updated or deleted, even by their creator', function () {
    $user = User::factory()->create();
    Receptionist::create(['user_id' => $user->id]);
    $consent = Consent::factory()->create(['recorded_by' => $user->id]);

    expect($user->can('update', $consent))->toBeFalse()
        ->and($user->can('delete', $consent))->toBeFalse();
});

test('admin can update or delete consents via the before() bypass', function () {
    $consent = Consent::factory()->create();

    expect(adminUser()->can('update', $consent))->toBeTrue()
        ->and(adminUser()->can('delete', $consent))->toBeTrue();
});
