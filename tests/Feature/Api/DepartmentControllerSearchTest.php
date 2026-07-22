<?php

use App\Models\EmergencyDoctor;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

beforeEach(function () {
    $this->doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $this->doctor->id]);
    $this->actingAs($this->doctor);
});

test('search by full so_number matches the service order', function () {
    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'so_number' => 'PS/2026/07/2620/EMG/00001334']);
    ServiceOrder::factory()->create(['type' => 'EMG', 'so_number' => 'PS/2026/07/2621/EMG/00001335']);

    $response = $this->postJson('/api/emg/search', ['q' => 'PS/2026/07/2620/EMG', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('search by plain ps_number matches the patient\'s service orders', function () {
    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/07/2620']);
    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'patient_id' => $patient->id]);
    ServiceOrder::factory()->create(['type' => 'EMG']);

    $response = $this->postJson('/api/emg/search', ['q' => 'PS/2026/07/2620', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('search by SO-prefixed term matches so_number', function () {
    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'so_number' => 'SO-99887766']);
    ServiceOrder::factory()->create(['type' => 'EMG']);

    $response = $this->postJson('/api/emg/search', ['q' => 'SO-9988', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('search by TR-prefixed term resolves the service order via its transaction', function () {
    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/07/22/0001']);
    $match = ServiceOrder::factory()->create(['type' => 'EMG']);
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $match->id,
        'income_or_expense' => 'INCOME',
    ]);
    ServiceOrder::factory()->create(['type' => 'EMG']);

    $response = $this->postJson('/api/emg/search', ['q' => 'TR/2026/07/22', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('search by all-digit term matches so_short', function () {
    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => '00001334']);
    ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => '00009999']);

    $response = $this->postJson('/api/emg/search', ['q' => '000013', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('search by alphabetic term matches patient name', function () {
    $patient = Patient::factory()->create(['name' => 'Ayesha Khan']);
    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'patient_id' => $patient->id]);
    ServiceOrder::factory()->create(['type' => 'EMG']);

    $response = $this->postJson('/api/emg/search', ['q' => 'Ayesha', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});
