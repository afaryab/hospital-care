<?php

use App\Models\Icd10Code;
use App\Models\IndDoctor;
use App\Models\OpdDoctor;
use App\Models\ServiceOrder;
use App\Models\User;

test('a deactivated ICD-10 code is rejected when saving an OPD treatment record', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $inactive = Icd10Code::factory()->create(['is_active' => false]);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'OPD', 'doctor_id' => $doctor->id]);

    $response = $this->postJson("/api/opd/service-orders/{$serviceOrder->id}/treatment-record", [
        'icd10_code_id' => $inactive->id,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['icd10_code_id']);
});

test('an active ICD-10 code is accepted when saving a treatment record', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $active = Icd10Code::factory()->create(['is_active' => true]);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'DNT', 'doctor_id' => $doctor->id]);

    $response = $this->postJson("/api/dnt/service-orders/{$serviceOrder->id}/treatment-record", [
        'icd10_code_id' => $active->id,
    ]);

    $response->assertOk();
    expect($serviceOrder->fresh()->treatmentRecord->diagnosis_code)->toBe($active->code);
});

test('a deactivated ICD-10 code is rejected when saving an IND treatment record', function () {
    $doctor = User::factory()->create();
    IndDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $inactive = Icd10Code::factory()->create(['is_active' => false]);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'IND', 'doctor_id' => $doctor->id]);

    $response = $this->postJson("/api/ind/service-orders/{$serviceOrder->id}/treatment-record", [
        'icd10_code_id' => $inactive->id,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['icd10_code_id']);
});

test('a deactivated ICD-10 code is rejected when saving a shared-department (DepartmentController) treatment record', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $inactive = Icd10Code::factory()->create(['is_active' => false]);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'DNT', 'doctor_id' => $doctor->id]);

    $response = $this->postJson("/api/dnt/service-orders/{$serviceOrder->id}/treatment-record", [
        'icd10_code_id' => $inactive->id,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['icd10_code_id']);
});
