<?php

use App\Models\ExpenseVoucher;
use App\Models\LcdOpdOperator;
use App\Models\NursingStaff;
use App\Models\Patient;
use App\Models\PatientManager;
use App\Models\Receptionist;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('a nurse can view my payments and sees their own vouchers', function () {
    $user = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    $ownVoucher = ExpenseVoucher::factory()->create(['payed_to' => $user->id]);
    ExpenseVoucher::factory()->create(); // someone else's voucher

    get(route('my-payments'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('doctor/my-payments')
            ->where('mode', 'vouchers')
            ->has('vouchers.data', 1)
            ->where('vouchers.data.0.id', $ownVoucher->id)
        );
});

test('a receptionist can view my payments', function () {
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    get(route('my-payments'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('mode', 'vouchers'));
});

test('an LCD operator can view my payments', function () {
    $user = User::factory()->create();
    LcdOpdOperator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    get(route('my-payments'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('mode', 'vouchers'));
});

test('a patient manager sees transactions paid by their managed patients, not vouchers', function () {
    $user = User::factory()->create();
    $managedPatient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    PatientManager::factory()->create(['user_id' => $user->id, 'patient_id' => $managedPatient->id]);
    actingAs($user);

    $ownedTransaction = Transaction::factory()->create([
        'patient_id' => $managedPatient->id,
        'income_or_expense' => 'INCOME',
        'amount' => 750,
    ]);
    Transaction::factory()->create([
        'patient_id' => $otherPatient->id,
        'income_or_expense' => 'INCOME',
        'amount' => 999,
    ]);

    get(route('my-payments'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('doctor/my-payments')
            ->where('mode', 'patient_transactions')
            ->has('transactions.data', 1)
            ->where('transactions.data.0.id', $ownedTransaction->id)
            ->where('totals.paid', fn ($value) => (float) $value === 750.0)
        );
});
