<?php

use App\Models\OpdDoctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// ─── WebController::patient() ──────────────────────────────────────────────

test('a doctor with no relation to a patient cannot open that patient\'s record', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/03/0001']);

    get(route('patients-register-ps-number', ['year' => 2026, 'month' => '03', 'number' => '0001']))
        ->assertForbidden();
});

test('a receptionist can open any patient\'s record', function () {
    $receptionist = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $receptionist->id]);
    actingAs($receptionist);

    Patient::factory()->create(['ps_number' => 'PS/2026/03/0001']);

    get(route('patients-register-ps-number', ['year' => 2026, 'month' => '03', 'number' => '0001']))
        ->assertOk();
});

test('a doctor can open the record of a patient they have treated', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/03/0001']);
    ServiceOrder::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    get(route('patients-register-ps-number', ['year' => 2026, 'month' => '03', 'number' => '0001']))
        ->assertOk();
});

// ─── WebController::updateServiceOrderStatus() ─────────────────────────────

test('a doctor cannot change the status of a service order assigned to someone else', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $otherDoctor = User::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create(['doctor_id' => $otherDoctor->id]);

    $this->patch(route('service-orders.update-status', $serviceOrder), ['status' => 'CLOSED'])
        ->assertForbidden();
});

test('the assigned doctor can change the status of their own service order', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['doctor_id' => $doctor->id]);

    $this->patch(route('service-orders.update-status', $serviceOrder), ['status' => 'CLOSED'])
        ->assertRedirect();

    expect($serviceOrder->fresh()->status)->toBe('CLOSED');
});

// ─── Prints\TransactionPdfPrintController ──────────────────────────────────

test('a doctor with no relation to a transaction cannot stream or download its pdf', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/15/0001']);

    get(route('print-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']))
        ->assertForbidden();
    get(route('download-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']))
        ->assertForbidden();
});

test('a receptionist can stream a transaction pdf', function () {
    $receptionist = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $receptionist->id]);
    actingAs($receptionist);

    Transaction::factory()->create(['tr_number' => 'TR/2026/03/15/0001']);

    get(route('print-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']))
        ->assertOk();
});

test('a doctor assigned to a transaction element can stream that transaction\'s pdf', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/15/0001']);
    TransactionElement::factory()->create(['transaction_id' => $transaction->id, 'doctor_id' => $doctor->id]);

    get(route('print-transaction', ['year' => 2026, 'month' => '03', 'day' => '15', 'number' => '0001']))
        ->assertOk();
});
