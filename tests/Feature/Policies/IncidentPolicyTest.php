<?php

use App\Models\Incident;
use App\Models\OpdDoctor;
use App\Models\Receptionist;
use App\Models\User;

test('admin can view, view any, create, update, and delete incidents', function () {
    $incident = Incident::factory()->create();
    $admin = adminUser();

    expect($admin->can('view', $incident))->toBeTrue()
        ->and($admin->can('viewAny', Incident::class))->toBeTrue()
        ->and($admin->can('create', Incident::class))->toBeTrue()
        ->and($admin->can('update', $incident))->toBeTrue()
        ->and($admin->can('delete', $incident))->toBeTrue();
});

test('a receptionist can view and create incidents but not update or delete them', function () {
    $user = User::factory()->create();
    Receptionist::create(['user_id' => $user->id]);
    $incident = Incident::factory()->create();

    expect($user->can('view', $incident))->toBeTrue()
        ->and($user->can('create', Incident::class))->toBeTrue()
        ->and($user->can('update', $incident))->toBeFalse()
        ->and($user->can('delete', $incident))->toBeFalse();
});

test('a doctor can view and create incidents but not manage the lifecycle', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $incident = Incident::factory()->create();

    expect($doctor->can('view', $incident))->toBeTrue()
        ->and($doctor->can('create', Incident::class))->toBeTrue()
        ->and($doctor->can('update', $incident))->toBeFalse();
});

test('a user with no staff profile cannot view or create incidents', function () {
    $user = User::factory()->create();
    $incident = Incident::factory()->create();

    expect($user->can('view', $incident))->toBeFalse()
        ->and($user->can('create', Incident::class))->toBeFalse();
});
