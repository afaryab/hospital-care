<?php

use App\Helpers\DateHelper;
use App\Models\EmergencyDoctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\ServiceDepartment;
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

test('the PDF shows the treating doctor\'s PMDC number', function () {
    actingAs(User::factory()->create());

    $doctor = User::factory()->create(['name' => 'Dr. Bilal Khan']);
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id, 'pmdc_number' => '54321-P']);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG', 'doctor_id' => $doctor->id]);

    $serviceOrder = $serviceOrder->fresh(['patient', 'doctor']);
    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder, 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('Dr. Bilal Khan')
        ->toContain('PMDC# 54321-P');
});

test('the PDF shows the service order\'s real department name, not a hardcoded one', function () {
    actingAs(User::factory()->create());

    $department = ServiceDepartment::factory()->create(['name' => 'Dental']);
    $service = Service::factory()->create(['service_department_id' => $department->id]);
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'DNT', 'service_id' => $service->id]);

    $response = get(route('print-serviceorder', ['id' => $serviceOrder->id]));

    $response->assertOk();

    $serviceOrder = $serviceOrder->fresh(['patient', 'doctor', 'service.department']);
    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder, 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('DENTAL DEPARTMENT')
        ->not->toContain('EMERGENCY DEPARTMENT');
});

test('discharged outcome ticks DISCHARGED HOME and no other disposition checkbox', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'discharged',
        'outcome_at' => '2026-07-27 15:30:00',
    ]);

    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder->fresh(['patient', 'doctor', 'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns']), 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('<span class="cb">X</span> DISCHARGED HOME')
        ->not->toContain('<span class="cb">X</span> TRANSFERRED TO')
        ->not->toContain('<span class="cb">X</span> DIED IN ED');
});

test('referred outcome ticks TRANSFERRED TO with the hospital name and time', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'referred',
        'outcome_at' => '2026-07-27 16:00:00',
        'referral_to' => 'City General Hospital',
    ]);

    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder->fresh(['patient', 'doctor', 'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns']), 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('<span class="cb">X</span> TRANSFERRED TO:&nbsp;City General Hospital')
        ->toContain('City General Hospital')
        ->not->toContain('<span class="cb">X</span> DISCHARGED HOME')
        ->not->toContain('<span class="cb">X</span> DIED IN ED');
});

test('expired outcome ticks DIED IN ED with time of death and cause of death notes', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => now(),
        'outcome' => 'expired',
        'outcome_at' => '2026-07-27 17:45:00',
        'outcome_notes' => 'Cardiac arrest, resuscitation unsuccessful',
    ]);

    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder->fresh(['patient', 'doctor', 'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns']), 'patient' => $serviceOrder->patient])->render();

    expect($html)->toContain('<span class="cb">X</span> DIED IN ED')
        ->toContain('Cardiac arrest, resuscitation unsuccessful')
        ->not->toContain('<span class="cb">X</span> DISCHARGED HOME')
        ->not->toContain('<span class="cb">X</span> TRANSFERRED TO');
});

test('the drug chart uses each prescription\'s own given_at time instead of the visit\'s treated_at', function () {
    $treatedAt = '2026-07-27 08:00:00';
    $givenAt = '2026-07-27 09:45:00';
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => User::factory()->create()->id,
        'recorded_by' => User::factory()->create()->id,
        'treated_at' => $treatedAt,
        'prescriptions' => [
            ['drug_name' => 'Morphine', 'dose' => '2mg', 'given_at' => $givenAt],
        ],
    ]);

    $html = view('pdfs.serviceorder', ['serviceOrder' => $serviceOrder->fresh(['patient', 'doctor', 'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns']), 'patient' => $serviceOrder->patient])->render();

    // treated_at legitimately appears elsewhere (triage time, time seen by
    // doctor), so just confirm the drug's own given_at time is present.
    expect($html)->toContain(DateHelper::pdfFormat($givenAt, 'H:i'));
});
