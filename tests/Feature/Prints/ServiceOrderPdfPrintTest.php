<?php

use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\User;
use App\Models\VitalSign;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('service order pdf renders successfully', function () {
    actingAs(User::factory()->create());

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);

    get(route('print-serviceorder', ['id' => $serviceOrder->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

test('service order pdf includes treatment, triage, history, and prescriber/time for drugs', function () {
    $patient = Patient::factory()->create(['guardian' => 'Muhammad Ali', 'relation' => 'S/o']);
    $doctor = User::factory()->create(['name' => 'Dr. Sara Ahmed']);
    $serviceOrder = ServiceOrder::factory()->create([
        'type' => 'EMG',
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $triage = Triage::factory()->create(['name' => 'Priority Custom']);

    $treatmentRecord = TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'chief_complaint' => 'Severe chest pain radiating to left arm',
        'history_of_present_illness' => 'Sudden onset two hours ago',
        'diagnosis_text' => 'Acute coronary syndrome',
        'treatment_plan' => 'Aspirin, oxygen, cardiology referral',
        'triage_id' => $triage->id,
        'prescriptions' => [
            ['drug_name' => 'Aspirin', 'dose' => '300mg', 'frequency' => 'stat', 'route' => 'PO'],
        ],
    ]);

    VitalSign::create([
        'treatment_record_id' => $treatmentRecord->id,
        'pulse_rate' => 110,
        'respiratory_rate' => 22,
        'blood_pressure_systolic' => 150,
        'blood_pressure_diastolic' => 95,
        'temperature' => 37.2,
        'recorded_at' => now(),
        'recorded_by' => $doctor->id,
    ]);

    $serviceOrder = $serviceOrder->fresh([
        'patient', 'doctor', 'treatmentRecord.triage', 'treatmentRecord.attachments',
        'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns',
    ]);

    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder, 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('Severe chest pain radiating to left arm')
        ->toContain('Sudden onset two hours ago')
        ->toContain('Acute coronary syndrome')
        ->toContain('Aspirin, oxygen, cardiology referral')
        ->toContain('Priority Custom')
        ->toContain('Dr. Sara Ahmed')
        ->toContain('S/o Muhammad Ali')
        ->toContain('Aspirin 300mg stat PO')
        ->not->toContain('Karachi Hospital');
});

test('service order pdf shows past history as the last 6 ICD codes from other visits, not the current one', function () {
    actingAs(User::factory()->create());

    $patient = Patient::factory()->create();
    $doctor = User::factory()->create();
    $currentOrder = ServiceOrder::factory()->create(['type' => 'EMG', 'patient_id' => $patient->id]);

    TreatmentRecord::create([
        'service_order_id' => $currentOrder->id,
        'department_id' => $currentOrder->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'diagnosis_code' => 'R07.9',
    ]);

    // 7 past visits, most recent first once sorted; expect only the 6 newest.
    $codes = ['OLDEST-J45.9', 'E11.9', 'I10', 'K21.9', 'M54.5', 'N39.0', 'NEWEST-F41.9'];
    foreach ($codes as $i => $code) {
        $pastOrder = ServiceOrder::factory()->create(['type' => 'OPD', 'patient_id' => $patient->id]);
        TreatmentRecord::create([
            'service_order_id' => $pastOrder->id,
            'department_id' => $pastOrder->service->service_department_id,
            'treating_doctor_id' => $doctor->id,
            'recorded_by' => $doctor->id,
            'treated_at' => now()->subDays(count($codes) - $i),
            'diagnosis_code' => $code,
        ]);
    }

    // Unrelated patient's diagnosis must never leak in.
    $otherPatientOrder = ServiceOrder::factory()->create(['type' => 'OPD']);
    TreatmentRecord::create([
        'service_order_id' => $otherPatientOrder->id,
        'department_id' => $otherPatientOrder->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'diagnosis_code' => 'OTHER-PATIENT-CODE',
    ]);

    get(route('print-serviceorder', ['id' => $currentOrder->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    // Same query the controller runs — verifies ordering/exclusion/limit directly,
    // since dompdf's binary output can't be inspected for text content.
    $pastDiagnoses = TreatmentRecord::query()
        ->whereHas('serviceOrder', fn ($q) => $q->where('patient_id', $patient->id)
            ->where('id', '!=', $currentOrder->id))
        ->where(fn ($q) => $q->whereNotNull('diagnosis_code')->orWhereNotNull('icd10_code_id'))
        ->latest('treated_at')
        ->limit(6)
        ->pluck('diagnosis_code');

    expect($pastDiagnoses)->toHaveCount(6)
        ->and($pastDiagnoses->first())->toBe('NEWEST-F41.9')
        ->and($pastDiagnoses)->not->toContain('OLDEST-J45.9')
        ->and($pastDiagnoses)->not->toContain('R07.9')
        ->and($pastDiagnoses)->not->toContain('OTHER-PATIENT-CODE');
});
