<?php

use App\Models\Administrator;
use App\Models\Receptionist;
use App\Models\Transaction;
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
