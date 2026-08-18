<?php

use App\Models\Administrator;
use App\Models\OpdDoctor;
use App\Models\Receptionist;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

test('admin can do anything with transactions', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    $transaction = Transaction::factory()->create();

    expect($admin->can('view', $transaction))->toBeTrue()
        ->and($admin->can('create', Transaction::class))->toBeTrue()
        ->and($admin->can('update', $transaction))->toBeTrue()
        ->and($admin->can('delete', $transaction))->toBeTrue();
});

test('receptionist can create transactions', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);

    expect($receptionist->can('create', Transaction::class))->toBeTrue();
});

test('receptionist can only update own transactions', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);

    $ownTransaction = Transaction::factory()->create(['created_by' => $receptionist->id]);
    $otherTransaction = Transaction::factory()->create();

    expect($receptionist->can('update', $ownTransaction))->toBeTrue()
        ->and($receptionist->can('update', $otherTransaction))->toBeFalse();
});

test('receptionist can view any transaction', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);
    $transaction = Transaction::factory()->create();

    expect($receptionist->can('view', $transaction))->toBeTrue();
});

test('user without profile cannot access transactions', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create();

    expect($user->can('view', $transaction))->toBeFalse()
        ->and($user->can('create', Transaction::class))->toBeFalse();
});

test('a doctor cannot view a transaction with no element assigned to them', function () {
    $doctor = User::factory()->create();
    OpdDoctor::create(['user_id' => $doctor->id]);
    $transaction = Transaction::factory()->create();

    expect($doctor->can('view', $transaction))->toBeFalse();
});

test('a doctor can view a transaction containing an element assigned to them', function () {
    $doctor = User::factory()->create();
    OpdDoctor::create(['user_id' => $doctor->id]);
    $transaction = Transaction::factory()->create();
    TransactionElement::factory()->create(['transaction_id' => $transaction->id, 'doctor_id' => $doctor->id]);

    expect($doctor->can('view', $transaction))->toBeTrue();
});
