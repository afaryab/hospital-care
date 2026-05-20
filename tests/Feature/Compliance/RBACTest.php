<?php

use App\Models\Administrator;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\OpdDoctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('receptionist cannot access admin panel', function () {
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    $user->assignRole('receptionist');

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

test('doctor cannot access accounts panel', function () {
    $user = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $user->id]);
    $user->assignRole('opd_doctor');

    expect($user->canAccessPanel(Filament::getPanel('accounts')))->toBeFalse();
});

test('administrator can access all guarded resources', function () {
    $user = User::factory()->create();
    Administrator::factory()->create(['user_id' => $user->id]);
    $user->assignRole('administrator');

    $patient = Patient::factory()->create();

    expect($user->canAccessPanel(Filament::getPanel('admin')))->toBeTrue()
        ->and(Gate::forUser($user)->allows('view', $patient))->toBeTrue()
        ->and(Gate::forUser($user)->allows('delete', $patient))->toBeTrue();
});

test('receptionist can create transactions but not edit closings from another user', function () {
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    $user->assignRole('receptionist');

    $anotherUser = User::factory()->create();
    $closing = Closing::factory()->create(['receptionist_id' => $anotherUser->id]);

    expect(Gate::forUser($user)->allows('create', Transaction::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('update', $closing))->toBeFalse();
});

test('doctor can view service orders but cannot create transactions', function () {
    $user = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $user->id]);
    $user->assignRole('opd_doctor');

    $serviceOrder = ServiceOrder::factory()->create();

    expect(Gate::forUser($user)->allows('view', $serviceOrder))->toBeTrue()
        ->and(Gate::forUser($user)->allows('create', Transaction::class))->toBeFalse();
});

test('unauthenticated user cannot access protected route', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('accountant permissions are enforced by policies', function () {
    $user = User::factory()->create();
    $user->assignRole('accountant');

    $expenseVoucher = ExpenseVoucher::factory()->create();

    expect(Gate::forUser($user)->allows('view', $expenseVoucher))->toBeTrue()
        ->and(Gate::forUser($user)->allows('update', $expenseVoucher))->toBeTrue()
        ->and(Gate::forUser($user)->allows('create', Transaction::class))->toBeFalse();
});
