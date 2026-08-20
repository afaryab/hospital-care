<?php

use App\Models\Consent;
use App\Models\EmergencyDoctor;
use App\Models\HospitalSetting;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('treatment records can be created without a recorded consent when the gate is disabled (default)', function () {
    expect(HospitalSetting::get('require_consent_before_treatment', false))->toBeFalse();

    $record = TreatmentRecord::factory()->create();

    expect($record->exists)->toBeTrue();
});

test('treatment record creation is blocked without a recorded consent when the gate is enabled', function () {
    HospitalSetting::set('require_consent_before_treatment', true);

    $serviceOrder = ServiceOrder::factory()->create();

    expect(fn () => TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]))
        ->toThrow(ValidationException::class);

    expect(TreatmentRecord::where('service_order_id', $serviceOrder->id)->exists())->toBeFalse();
});

test('treatment record creation succeeds when the gate is enabled and a treatment consent exists', function () {
    HospitalSetting::set('require_consent_before_treatment', true);

    $serviceOrder = ServiceOrder::factory()->create();
    Consent::factory()->treatment()->create(['patient_id' => $serviceOrder->patient_id]);

    $record = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);

    expect($record->exists)->toBeTrue();
});

test('the gate only accepts a treatment-type consent, not procedure or data_sharing', function () {
    HospitalSetting::set('require_consent_before_treatment', true);

    $serviceOrder = ServiceOrder::factory()->create();
    Consent::factory()->create(['patient_id' => $serviceOrder->patient_id, 'consent_type' => 'procedure']);

    expect(fn () => TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]))
        ->toThrow(ValidationException::class);
});

test('the gate blocks treatment via the real API endpoint when enabled', function () {
    HospitalSetting::set('require_consent_before_treatment', true);

    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG', 'doctor_id' => $doctor->id]);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'chief_complaint' => 'Chest pain',
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['consent']);
});

test('the gate allows treatment via the real API endpoint once consent is recorded', function () {
    HospitalSetting::set('require_consent_before_treatment', true);

    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG', 'doctor_id' => $doctor->id]);
    $triage = Triage::factory()->create();
    Consent::factory()->treatment()->create(['patient_id' => $serviceOrder->patient_id]);

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'chief_complaint' => 'Chest pain',
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
    ]);

    $response->assertOk();
});
