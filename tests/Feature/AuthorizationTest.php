<?php

use App\Models\Closing;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Transaction;
use App\Models\User;

// ─── Helpers ─────────────────────────────────────────────────────────────────

function makeReception(): Reception
{
    return Reception::create([
        'name' => 'Main Reception',
        'is_allowed_to_pay_voucher' => 0,
        'is_allowed_to_pay_from_petty_cash' => 0,
        'is_cash_allowed' => 1,
        'is_cheques_allowed' => 0,
        'is_card_allowed' => 0,
    ]);
}

function makeClosing(int $receptionId, int $receptionistId, string $ctNumber = 'CT/2026/03/0001'): Closing
{
    return Closing::create([
        'ct_number' => $ctNumber,
        'reception_id' => $receptionId,
        'receptionist_id' => $receptionistId,
    ]);
}

// ─── API Routes — Unauthenticated Access ─────────────────────────────────────

test('unauthenticated users cannot search patients via api', function () {
    $this->postJson('/api/patients')->assertUnauthorized();
});

test('unauthenticated users cannot search transactions via api', function () {
    $this->postJson('/api/transactions/search')->assertUnauthorized();
});

test('unauthenticated users cannot search closings via api', function () {
    $this->postJson('/api/closings/search')->assertUnauthorized();
});

test('unauthenticated users cannot search users via api', function () {
    $this->postJson('/api/users/search')->assertUnauthorized();
});

test('unauthenticated users cannot search service orders via api', function () {
    $this->postJson('/api/service-orders/search')->assertUnauthorized();
});

test('unauthenticated users cannot access lookup via api', function () {
    $this->getJson('/api/lookup')->assertUnauthorized();
});

// ─── ClosingPolicy ────────────────────────────────────────────────────────────

test('admin can view any closing', function () {
    $admin = User::factory()->create();
    $admin->adminProfiles()->create(['authority' => 'administrator']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, User::factory()->create()->id);

    expect($admin->can('view', $closing))->toBeTrue();
});

test('receptionist can view any closing', function () {
    $receptionist = User::factory()->create();
    $receptionist->receptionistProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, User::factory()->create()->id);

    expect($receptionist->can('view', $closing))->toBeTrue();
});

test('accountant can view any closing', function () {
    $accountant = User::factory()->create();
    $accountant->accountantProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, User::factory()->create()->id);

    expect($accountant->can('view', $closing))->toBeTrue();
});

test('receptionist can only update their own closing', function () {
    $owner = User::factory()->create();
    $owner->receptionistProfiles()->create(['authority' => 'assistant']);

    $other = User::factory()->create();
    $other->receptionistProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, $owner->id);

    expect($owner->can('update', $closing))->toBeTrue();
    expect($other->can('update', $closing))->toBeFalse();
});

test('admin can update any closing', function () {
    $admin = User::factory()->create();
    $admin->adminProfiles()->create(['authority' => 'administrator']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, User::factory()->create()->id);

    expect($admin->can('update', $closing))->toBeTrue();
});

test('nobody but admin can delete a closing', function () {
    $admin = User::factory()->create();
    $admin->adminProfiles()->create(['authority' => 'administrator']);

    $receptionist = User::factory()->create();
    $receptionist->receptionistProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, $receptionist->id);

    expect($admin->can('delete', $closing))->toBeTrue();
    expect($receptionist->can('delete', $closing))->toBeFalse();
});

// ─── TransactionPolicy ────────────────────────────────────────────────────────

test('creator receptionist can update their own transaction', function () {
    $receptionist = User::factory()->create();
    $receptionist->receptionistProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, $receptionist->id);
    $transaction = Transaction::create([
        'tr_number' => 'TR/2026/03/28/0001',
        'closing_id' => $closing->id,
        'created_by' => $receptionist->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
        'amount_alphabetical' => 'Five Hundred',
    ]);

    expect($receptionist->can('update', $transaction))->toBeTrue();
});

test('receptionist cannot update a transaction created by another user', function () {
    $creator = User::factory()->create();
    $creator->receptionistProfiles()->create(['authority' => 'assistant']);

    $other = User::factory()->create();
    $other->receptionistProfiles()->create(['authority' => 'assistant']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, $creator->id);
    $transaction = Transaction::create([
        'tr_number' => 'TR/2026/03/28/0001',
        'closing_id' => $closing->id,
        'created_by' => $creator->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
        'amount_alphabetical' => 'Five Hundred',
    ]);

    expect($other->can('update', $transaction))->toBeFalse();
});

test('admin can update any transaction', function () {
    $admin = User::factory()->create();
    $admin->adminProfiles()->create(['authority' => 'administrator']);

    $reception = makeReception();
    $closing = makeClosing($reception->id, User::factory()->create()->id);
    $transaction = Transaction::create([
        'tr_number' => 'TR/2026/03/28/0001',
        'closing_id' => $closing->id,
        'created_by' => User::factory()->create()->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
        'amount_alphabetical' => 'Five Hundred',
    ]);

    expect($admin->can('update', $transaction))->toBeTrue();
});

// ─── PatientPolicy ────────────────────────────────────────────────────────────

test('any staff profile can view patient records', function () {
    $doctor = User::factory()->create();
    $doctor->opdDoctorProfiles()->create(['authority' => 'assistant']);

    $patient = Patient::create([
        'ps_number' => 'PS/2026/03/0001',
        'name' => 'Test Patient',
        'gender' => 'm',
    ]);

    expect($doctor->can('view', $patient))->toBeTrue();
});

test('receptionist can create and update patients', function () {
    $receptionist = User::factory()->create();
    $receptionist->receptionistProfiles()->create(['authority' => 'assistant']);

    $patient = Patient::create([
        'ps_number' => 'PS/2026/03/0001',
        'name' => 'Test Patient',
        'gender' => 'm',
    ]);

    expect($receptionist->can('create', Patient::class))->toBeTrue();
    expect($receptionist->can('update', $patient))->toBeTrue();
});

test('doctor cannot create or update patients', function () {
    $doctor = User::factory()->create();
    $doctor->opdDoctorProfiles()->create(['authority' => 'assistant']);

    $patient = Patient::create([
        'ps_number' => 'PS/2026/03/0001',
        'name' => 'Test Patient',
        'gender' => 'm',
    ]);

    expect($doctor->can('create', Patient::class))->toBeFalse();
    expect($doctor->can('update', $patient))->toBeFalse();
});
