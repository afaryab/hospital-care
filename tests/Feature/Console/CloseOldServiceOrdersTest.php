<?php

use App\Models\ServiceOrder;
use App\Models\Triage;
use App\Models\TriageHistory;
use App\Models\User;

test('EMG orders open for 12+ hours with no treatment record are auto-closed and defaulted to green triage', function () {
    $doctor = User::factory()->create();
    $order = ServiceOrder::factory()->create([
        'type' => 'EMG',
        'status' => 'open',
        'doctor_id' => $doctor->id,
        'created_at' => now()->subHours(13),
    ]);

    $this->artisan('app:close-old-service-orders');

    $order->refresh();
    expect($order->status)->toBe('closed');
    expect($order->closed_at)->not->toBeNull();

    $treatmentRecord = $order->treatmentRecord;
    expect($treatmentRecord)->not->toBeNull();

    $greenTriage = Triage::where('color', 'green')->first();
    expect($treatmentRecord->triage_id)->toBe($greenTriage->id);

    $history = TriageHistory::where('service_order_id', $order->id)->first();
    expect($history)->not->toBeNull();
    expect($history->new_triage_id)->toBe($greenTriage->id);
    expect($history->old_triage_id)->toBeNull();
});

test('EMG orders open for less than 12 hours are left untouched', function () {
    $order = ServiceOrder::factory()->create([
        'type' => 'EMG',
        'status' => 'open',
        'created_at' => now()->subHours(6),
    ]);

    $this->artisan('app:close-old-service-orders');

    $order->refresh();
    expect($order->status)->toBe('open');
    expect($order->closed_at)->toBeNull();
});

test('EMG orders with an existing treatment record are not auto-closed', function () {
    $doctor = User::factory()->create();
    $order = ServiceOrder::factory()->create([
        'type' => 'EMG',
        'status' => 'open',
        'doctor_id' => $doctor->id,
        'created_at' => now()->subHours(13),
    ]);

    $order->treatmentRecord()->create([
        'department_id' => $order->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'chief_complaint' => 'Chest pain',
    ]);

    $this->artisan('app:close-old-service-orders');

    $order->refresh();
    expect($order->status)->toBe('open');
    expect($order->closed_at)->toBeNull();
});
