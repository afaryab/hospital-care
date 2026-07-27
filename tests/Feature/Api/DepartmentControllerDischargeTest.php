<?php

use App\Models\EmergencyDoctor;
use App\Models\NursingStaff;
use App\Models\ServiceOrder;
use App\Models\Triage;
use App\Models\User;

test('finalizing an EMG treatment record requires an outcome', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'finalize' => true,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['outcome', 'outcome_at']);
});

test('finalizing with outcome=referred requires referral_to', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'outcome' => 'referred',
        'outcome_at' => now()->toIso8601String(),
        'finalize' => true,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['referral_to']);
});

test('a doctor can discharge an EMG patient with a full disposition', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'outcome' => 'discharged',
        'outcome_at' => now()->toIso8601String(),
        'outcome_notes' => 'Stable, advised rest',
        'finalize' => true,
    ]);

    $response->assertOk();
    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect($treatmentRecord->outcome->value)->toBe('discharged');
    expect($treatmentRecord->outcome_notes)->toBe('Stable, advised rest');
    expect($treatmentRecord->is_finalized)->toBeTrue();
});

test('a nursing-staff-only user cannot discharge (finalize) an EMG patient', function () {
    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);
    $this->actingAs($nurse);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'outcome' => 'discharged',
        'outcome_at' => now()->toIso8601String(),
        'finalize' => true,
    ]);

    $response->assertForbidden();
    expect($serviceOrder->fresh()->treatmentRecord?->is_finalized ?? false)->toBeFalse();
});

test('a nursing-staff-only user can chart a draft with vitals and a timed prescription', function () {
    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);
    $this->actingAs($nurse);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();
    $givenAt = now()->subMinutes(10)->toIso8601String();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'chief_complaint' => 'Monitoring vitals',
        'prescriptions' => [
            ['drug_name' => 'Paracetamol', 'dose' => '500mg', 'given_at' => $givenAt],
        ],
        'vitals' => ['pulse_rate' => 88],
        'finalize' => false,
    ]);

    $response->assertOk();
    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect($treatmentRecord->is_finalized)->toBeFalse();
    expect($treatmentRecord->prescriptions[0]['given_at'])->toBe($givenAt);
    expect($treatmentRecord->vitalSigns()->count())->toBe(1);
});

test('setting triage to a black-colored triage still requires doctor authorization to finalize', function () {
    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);
    $this->actingAs($nurse);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $blackTriage = Triage::factory()->create(['color' => 'black', 'name' => 'Custom Code Black']);

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $blackTriage->id,
        'treated_at' => now()->toIso8601String(),
        'outcome' => 'expired',
        'outcome_at' => now()->toIso8601String(),
        'finalize' => true,
    ]);

    $response->assertForbidden();
});

test('finalizing non-EMG departments does not require an outcome or doctor authorization', function () {
    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);
    $this->actingAs($nurse);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'DNT']);

    $response = $this->postJson("/api/dnt/service-orders/{$serviceOrder->id}/treatment-record", [
        'finalize' => true,
    ]);

    $response->assertOk();
});
