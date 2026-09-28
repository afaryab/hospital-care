<?php

use App\Enum\ServiceOrderTemplate;
use App\Enum\TreatmentOutcome;
use App\Helpers\PatientDisplay;
use App\Helpers\PrescriptionFormatter;
use App\Http\Controllers\Prints\ServiceOrderPdfPrintController;
use App\Models\Drug;
use App\Models\EmergencyDoctor;
use App\Models\Icd10Code;
use App\Models\Patient;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

function emgOrder(array $patient = []): ServiceOrder
{
    $department = ServiceDepartment::query()->where('slug', 'EMG')->first()
        ?? ServiceDepartment::factory()->create(['slug' => 'EMG', 'name' => 'Emergency']);
    $service = Service::factory()->create(['service_department_id' => $department->id]);

    return ServiceOrder::factory()->create([
        'type' => 'EMG',
        'service_id' => $service->id,
        'patient_id' => Patient::factory()->create($patient)->id,
        'so_short' => 'EMG/00000042',
    ]);
}

function renderTriage(ServiceOrder $order): string
{
    $order = $order->fresh(['patient', 'doctor', 'service.department', 'treatmentRecord.triage', 'treatmentRecord.treatingDoctor', 'treatmentRecord.vitalSigns', 'treatmentRecord.icd10Code']);

    return view(ServiceOrderPdfPrintController::resolveView($order), ['serviceOrder' => $order, 'patient' => $order->patient])->render();
}

// ── Template resolution ───────────────────────────────────────────────────

test('emergency orders resolve to the department triage view', function () {
    expect(ServiceOrderPdfPrintController::resolveView(emgOrder()))->toBe('pdfs.service_department.emg');
});

test('other departments fall back to their configured template, then the default', function () {
    $department = ServiceDepartment::factory()->create(['slug' => 'OPD', 'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact]);
    $order = ServiceOrder::factory()->create(['service_id' => Service::factory()->create(['service_department_id' => $department->id])->id]);

    expect(ServiceOrderPdfPrintController::resolveView($order))->toBe('pdfs.serviceorder-triage-compact');

    $department->update(['service_order_template' => null]);

    expect(ServiceOrderPdfPrintController::resolveView($order->fresh()))->toBe(ServiceOrderTemplate::default()->view());
});

test('a service-specific view wins over the department view', function () {
    $order = emgOrder();
    $order->service->update(['slug' => 'M.O_MOR']);
    $dir = sys_get_temp_dir().'/hc-views-'.uniqid();
    File::ensureDirectoryExists("{$dir}/pdfs/service");
    File::put("{$dir}/pdfs/service/m_o_mor.blade.php", 'service view');
    app('view')->getFinder()->prependLocation($dir);

    expect(ServiceOrderPdfPrintController::resolveView($order->fresh()))->toBe('pdfs.service.m_o_mor');

    File::deleteDirectory($dir);
});

// ── Helpers ───────────────────────────────────────────────────────────────

test('frequencies print as Morning + Noon + Night patterns from config', function (string $frequency, string $pattern, string $when) {
    expect(PrescriptionFormatter::pattern($frequency))->toBe($pattern)
        ->and(PrescriptionFormatter::when($frequency))->toBe($when);
})->with([
    ['TDS', '1 + 1 + 1', 'Morning, Noon, Night'],
    ['bd', '1 + 0 + 1', 'Morning, Night'],
    ['OD', '1 + 0 + 0', 'Morning'],
    ['HS', '0 + 0 + 1', 'Night (at bedtime)'],
    ['QID', '4 times/day', 'Four times a day'],
]);

test('dose quantity uses the dosage form, from the row or the drug catalogue', function () {
    Drug::query()->create(['name' => 'Ciprofloxacin 500', 'type' => 'Tablet', 'is_active' => true]);

    expect(PrescriptionFormatter::quantity(['drug_name' => 'Omeprazole', 'form' => 'Capsule']))->toBe('1 Capsule')
        ->and(PrescriptionFormatter::quantity(['drug_name' => 'Ciprofloxacin 500']))->toBe('1 Tablet')
        ->and(PrescriptionFormatter::quantity(['drug_name' => 'ORS', 'form' => 'sachet', 'dose' => '2 sachets']))->toBe('2 Sachet');
});

test('age prints with the right unit and sex spelled out', function () {
    expect(PatientDisplay::ageSex(new Patient(['age_dob' => now()->subYears(18)->subDays(3), 'gender' => 'm'])))->toBe('18 Y · Male')
        ->and(PatientDisplay::ageSex(new Patient(['age_dob' => now()->subMonths(7)->subDays(2), 'gender' => 'f'])))->toBe('7 M · Female')
        ->and(PatientDisplay::ageSex(new Patient(['age_dob' => now()->subDays(12), 'gender' => 't'])))->toBe('12 D · Transgender');
});

// ── Data capture ──────────────────────────────────────────────────────────

test('blood pressure sent as bp_systolic/bp_diastolic is now stored', function () {
    $record = TreatmentRecord::factory()->create();

    $vital = VitalSign::create([
        'treatment_record_id' => $record->id,
        'bp_systolic' => 130,
        'bp_diastolic' => 85,
        'recorded_at' => now(),
        'recorded_by' => User::factory()->create()->id,
    ]);

    expect($vital->fresh())
        ->blood_pressure_systolic->toBe(130)
        ->blood_pressure_diastolic->toBe(85)
        ->bp_systolic->toBe(130);
});

test('the emergency save stores BP, GCS, BSL, patient past history and ER details', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);
    $order = emgOrder();

    postJson("/api/emg/service-orders/{$order->id}/treatment-record", [
        'triage_id' => Triage::factory()->create()->id,
        'treated_at' => now()->toIso8601String(),
        'vitals' => ['bp_systolic' => 110, 'bp_diastolic' => 70, 'gcs' => 14, 'blood_glucose' => 145.5],
        'past_history' => ['htn' => true, 'dm' => false, 'asthma' => null, 'ihd' => null, 'allergies' => 'Penicillin'],
        'department_specific_data' => ['investigations_advised' => 'CBC, RFTs', 'advice' => 'Review after 3 days'],
        'prescriptions' => [['drug_name' => 'Ceftriaxone', 'dose' => '1g', 'route' => 'IV', 'given_in_er' => true, 'form' => 'Injection']],
    ])->assertOk();

    $vital = $order->fresh()->treatmentRecord->vitalSigns->last();
    $patient = $order->patient->fresh();

    expect($vital)->blood_pressure_systolic->toBe(110)->gcs->toBe(14)
        ->and((float) $vital->blood_glucose)->toBe(145.5)
        ->and($patient)->history_htn->toBeTrue()->history_dm->toBeFalse()->history_asthma->toBeNull()->allergies->toBe('Penicillin')
        ->and($order->fresh()->treatmentRecord->department_specific_data['investigations_advised'])->toBe('CBC, RFTs')
        ->and($order->fresh()->treatmentRecord->prescriptions[0]['given_in_er'])->toBeTrue();
});

test('admitted and LAMA are recordable discharge outcomes', function (string $outcome) {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);
    $order = emgOrder();

    postJson("/api/emg/service-orders/{$order->id}/treatment-record", [
        'triage_id' => Triage::factory()->create()->id,
        'treated_at' => now()->toIso8601String(),
        'finalize' => true,
        'outcome' => $outcome,
        'outcome_at' => now()->toIso8601String(),
    ])->assertOk();

    expect($order->fresh()->treatmentRecord->outcome->value)->toBe($outcome);
})->with(['admitted', 'lama']);

// ── Printed note ──────────────────────────────────────────────────────────

test('the triage note prints the reworked layout', function () {
    $order = emgOrder(['age_dob' => now()->subYears(18)->subDays(2), 'gender' => 'm', 'ps_number' => 'PS/2026/09/0077', 'history_htn' => true, 'history_dm' => false, 'allergies' => 'Sulfa drugs']);
    $icd = Icd10Code::factory()->create(['code' => 'T99.9', 'description' => 'Gastroenteritis']);
    $doctor = User::factory()->create(['name' => 'Dr. Hina']);

    $record = TreatmentRecord::create([
        'service_order_id' => $order->id,
        'department_id' => $order->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'chief_complaint' => 'Loose motions since morning',
        'history_of_present_illness' => 'Six episodes, no blood',
        'examination_findings' => ['Airway' => 'Patent', 'Breathing' => 'Spontaneous, no distress', 'General' => 'Dehydrated'],
        'icd10_code_id' => $icd->id,
        'diagnosis_code' => 'T99.9',
        'outcome' => TreatmentOutcome::Admitted,
        'department_specific_data' => ['investigations_advised' => 'Stool R/E', 'advice' => 'ORS after each stool', 'admitted_to' => 'Medical Ward'],
        'prescriptions' => [
            ['drug_name' => 'Metronidazole infusion', 'dose' => '500mg', 'route' => 'IV', 'given_in_er' => true],
            ['drug_name' => 'CIPROFLOXACIN 500MG EXTENDED RELEASE', 'form' => 'Tablet', 'frequency' => 'BD', 'duration' => '5 days', 'given_in_er' => false],
        ],
    ]);
    VitalSign::create(['treatment_record_id' => $record->id, 'bp_systolic' => 100, 'bp_diastolic' => 60, 'pulse_rate' => 110, 'recorded_at' => now(), 'recorded_by' => $doctor->id]);

    $html = renderTriage($order);

    expect($html)
        ->toContain('EMERGENCY · TRIAGE NOTE')
        ->toContain('PS/2026/09/0077')
        ->toContain('EMG/00000042')
        ->toContain('18 Y · Male')
        ->toContain('Presenting Complaints')
        ->toContain('Loose motions since morning')
        ->not->toContain('Complain:')
        ->not->toContain('HISTORY OF PRESENT ILLNESS')
        ->toContain('100/60 mmHg')
        ->toContain('Not recorded')
        ->toContain('Patent')
        ->toContain('Spontaneous, no distress')
        ->not->toContain('Clear Y/N')
        ->toContain('Sulfa drugs')
        ->toContain('Investigations Advised')
        ->toContain('Stool R/E')
        ->toContain('Metronidazole infusion')
        ->toContain('CIPROFLOXACIN 500MG EXTENDED RELEASE')
        ->toContain('1 Tablet')
        ->toContain('1 + 0 + 1')
        ->toContain('Morning, Night')
        ->toContain('1 + 1 + 1 = Morning + Noon + Night')
        ->toContain('Prescribed')
        ->not->toContain('PRESCRIBED BY')
        ->toContain('Gastroenteritis (T99.9)')
        ->toContain('ORS after each stool')
        ->toContain('Medical Ward');

    expect($html)->toMatch('/<span class="cb">X<\/span>Admitted to/');
});

test('the triage note streams as a PDF', function () {
    actingAs(adminUser());

    get(route('print-serviceorder', ['id' => emgOrder()->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});
