<?php

use App\Models\DeathCertificate;
use App\Models\Patient;
use App\Models\ReferralCertificate;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\User;

test('a death certificate is auto-created when outcome is set to expired on create', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'expired',
        'outcome_at' => now(),
    ]);

    expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->count())->toBe(1);
});

test('a death certificate is auto-created when outcome transitions to expired on update', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    $treatmentRecord = TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
    ]);

    expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->count())->toBe(0);

    $treatmentRecord->update(['outcome' => 'expired', 'outcome_at' => now()]);

    expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->count())->toBe(1);
});

test('a death certificate is not duplicated if the treatment record is saved again', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    $treatmentRecord = TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'expired',
        'outcome_at' => now(),
    ]);

    $treatmentRecord->touch(); // re-fires the saved() event without changing outcome

    expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->count())->toBe(1);
});

test('a death certificate is not created for non-expired outcomes', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'discharged',
        'outcome_at' => now(),
    ]);

    expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->exists())->toBeFalse();
});

test('the auto-created death certificate derives date/time of death, place, and informant from the record', function () {
    $patient = Patient::factory()->create(['guardian' => 'Muhammad Ali', 'relation' => 'S/o']);
    $department = ServiceDepartment::factory()->create(['name' => 'Emergency']);
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id]);
    $outcomeAt = now()->subHour();

    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $department->id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'expired',
        'outcome_at' => $outcomeAt,
    ]);

    $certificate = DeathCertificate::where('service_order_id', $serviceOrder->id)->first();

    expect($certificate->date_of_death->toDateString())->toBe($outcomeAt->toDateString())
        ->and($certificate->place_of_death)->toBe('Emergency')
        ->and($certificate->informant_name)->toBe('Muhammad Ali')
        ->and($certificate->informant_relation)->toBe('S/o');
});

test('a referral certificate is auto-created when outcome is set to referred', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'referred',
        'outcome_at' => now(),
        'referral_to' => 'City General Hospital',
    ]);

    $referral = ReferralCertificate::where('service_order_id', $serviceOrder->id)->first();

    expect($referral)->not->toBeNull()
        ->and($referral->receiving_facility_name)->toBe('City General Hospital');
});

test('a referral certificate is not duplicated on subsequent saves', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    $treatmentRecord = TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'referred',
        'outcome_at' => now(),
        'referral_to' => 'City General Hospital',
    ]);

    $treatmentRecord->touch();

    expect(ReferralCertificate::where('service_order_id', $serviceOrder->id)->count())->toBe(1);
});

test('neither certificate is created for improved/unchanged/deteriorated/discharged outcomes', function () {
    foreach (['improved', 'unchanged', 'deteriorated', 'discharged'] as $outcome) {
        $serviceOrder = ServiceOrder::factory()->create();

        TreatmentRecord::create([
            'service_order_id' => $serviceOrder->id,
            'department_id' => $serviceOrder->service->service_department_id,
            'treating_doctor_id' => User::factory()->create()->id,
            'recorded_by' => User::factory()->create()->id,
            'treated_at' => now(),
            'outcome' => $outcome,
            'outcome_at' => now(),
        ]);

        expect(DeathCertificate::where('service_order_id', $serviceOrder->id)->exists())->toBeFalse()
            ->and(ReferralCertificate::where('service_order_id', $serviceOrder->id)->exists())->toBeFalse();
    }
});
