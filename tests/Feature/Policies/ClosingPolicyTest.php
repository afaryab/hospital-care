<?php

use App\Models\Accountant;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\Receptionist;
use App\Models\User;

test('admin can do anything with closings', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    $closing = Closing::factory()->create();

    expect($admin->can('view', $closing))->toBeTrue()
        ->and($admin->can('create', Closing::class))->toBeTrue()
        ->and($admin->can('update', $closing))->toBeTrue()
        ->and($admin->can('delete', $closing))->toBeTrue();
});

test('receptionist can create and view closings', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);
    $closing = Closing::factory()->create();

    expect($receptionist->can('view', $closing))->toBeTrue()
        ->and($receptionist->can('create', Closing::class))->toBeTrue()
        ->and($receptionist->can('delete', $closing))->toBeFalse();
});

test('receptionist can only update own closings', function () {
    $receptionist = User::factory()->create();
    Receptionist::create(['user_id' => $receptionist->id]);

    $ownClosing = Closing::factory()->create(['receptionist_id' => $receptionist->id]);
    $otherClosing = Closing::factory()->create();

    expect($receptionist->can('update', $ownClosing))->toBeTrue()
        ->and($receptionist->can('update', $otherClosing))->toBeFalse();
});

test('accountant can view closings but not create', function () {
    $accountant = User::factory()->create();
    Accountant::create(['user_id' => $accountant->id]);
    $closing = Closing::factory()->create();

    expect($accountant->can('view', $closing))->toBeTrue()
        ->and($accountant->can('create', Closing::class))->toBeFalse();
});
