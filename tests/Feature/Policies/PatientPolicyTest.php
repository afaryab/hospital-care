<?php

use App\Models\Administrator;
use App\Models\Patient;
use App\Models\PatientManager;
use App\Models\Receptionist;
use App\Models\User;

test('admin can do anything with patients', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    $patient = Patient::factory()->create();

    expect($admin->can('view', $patient))->toBeTrue()
        ->and($admin->can('create', Patient::class))->toBeTrue()
        ->and($admin->can('update', $patient))->toBeTrue()
        ->and($admin->can('delete', $patient))->toBeTrue();
});

test('receptionist can create and update patients', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);
    $patient = Patient::factory()->create();

    expect($receptionist->can('view', $patient))->toBeTrue()
        ->and($receptionist->can('create', Patient::class))->toBeTrue()
        ->and($receptionist->can('update', $patient))->toBeTrue()
        ->and($receptionist->can('delete', $patient))->toBeFalse();
});

test('patient manager can create and update patients', function () {
    $manager = User::factory()->create();
    PatientManager::create(['user_id' => $manager->id]);
    $patient = Patient::factory()->create();

    expect($manager->can('view', $patient))->toBeTrue()
        ->and($manager->can('create', Patient::class))->toBeTrue()
        ->and($manager->can('update', $patient))->toBeTrue()
        ->and($manager->can('delete', $patient))->toBeFalse();
});

test('user without profile cannot view patients', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create();

    expect($user->can('view', $patient))->toBeFalse()
        ->and($user->can('create', Patient::class))->toBeFalse();
});
