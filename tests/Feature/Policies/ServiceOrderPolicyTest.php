<?php

use App\Models\Administrator;
use App\Models\NursingStaff;
use App\Models\OpdDoctor;
use App\Models\Receptionist;
use App\Models\ServiceOrder;
use App\Models\User;

test('admin can do anything with service orders', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    $serviceOrder = ServiceOrder::factory()->create();

    expect($admin->can('view', $serviceOrder))->toBeTrue()
        ->and($admin->can('update', $serviceOrder))->toBeTrue()
        ->and($admin->can('delete', $serviceOrder))->toBeTrue();
});

test('receptionist can view and update any service order', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);
    $serviceOrder = ServiceOrder::factory()->create();

    expect($receptionist->can('view', $serviceOrder))->toBeTrue()
        ->and($receptionist->can('update', $serviceOrder))->toBeTrue();
});

test('nursing staff can view and update any service order', function () {
    $nurse = User::factory()->create();
    NursingStaff::create(['user_id' => $nurse->id]);
    $serviceOrder = ServiceOrder::factory()->create();

    expect($nurse->can('view', $serviceOrder))->toBeTrue()
        ->and($nurse->can('update', $serviceOrder))->toBeTrue();
});

test('a doctor can view and update a service order assigned to a different doctor', function () {
    // Doctors aren't scoped to their own doctor_id — covering shifts, ward
    // rounds, and referrals all need a doctor to open a colleague's order.
    $doctor = User::factory()->create();
    OpdDoctor::create(['user_id' => $doctor->id]);
    $otherDoctor = User::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create(['doctor_id' => $otherDoctor->id]);

    expect($doctor->can('view', $serviceOrder))->toBeTrue()
        ->and($doctor->can('update', $serviceOrder))->toBeTrue();
});

test('a doctor can view and update a service order assigned to them', function () {
    $doctor = User::factory()->create();
    OpdDoctor::create(['user_id' => $doctor->id]);
    $serviceOrder = ServiceOrder::factory()->create(['doctor_id' => $doctor->id]);

    expect($doctor->can('view', $serviceOrder))->toBeTrue()
        ->and($doctor->can('update', $serviceOrder))->toBeTrue();
});

test('user without profile cannot access service orders', function () {
    $user = User::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create();

    expect($user->can('view', $serviceOrder))->toBeFalse()
        ->and($user->can('update', $serviceOrder))->toBeFalse();
});
