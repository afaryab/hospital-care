<?php

use App\Helpers\PiiHasher;
use App\Models\BirthCertificate;
use App\Models\DeathCertificate;
use App\Models\Patient;
use App\Models\PatientVersion;
use App\Models\ReferralCertificate;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\TreatmentRecordVersion;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('patient cnic contact and address are encrypted in database', function () {
    $patient = Patient::factory()->create([
        'cnic' => '35202-1234567-1',
        'contact' => '03001234567',
        'address' => 'Main Street 12',
    ]);

    $raw = DB::table('patients')->where('id', $patient->id)->first(['cnic', 'contact', 'address']);

    expect($raw->cnic)->not->toBe('35202-1234567-1')
        ->and($raw->contact)->not->toBe('03001234567')
        ->and($raw->address)->not->toBe('Main Street 12');
});

test('encrypted patient fields are transparently decrypted through model access', function () {
    $patient = Patient::factory()->create([
        'cnic' => '35202-1111111-1',
        'contact' => '03111234567',
        'address' => 'Street 42',
    ]);

    $patient->refresh();

    expect($patient->cnic)->toBe('35202-1111111-1')
        ->and($patient->contact)->toBe('03111234567')
        ->and($patient->address)->toBe('Street 42');
});

test('cnic duplicate check works with cnic hash', function () {
    $cnic = '35202-2222222-1';

    $patient = Patient::factory()->create([
        'cnic' => $cnic,
    ]);

    $hash = PiiHasher::cnic($cnic);

    expect($patient->cnic_hash)->toBe($hash)
        ->and(Patient::query()->where('cnic_hash', $hash)->count())->toBe(1);
});

test('cnic_hash is a keyed HMAC, not a plain unsalted SHA-256', function () {
    $patient = Patient::factory()->create(['cnic' => '35202-3333333-1']);

    expect($patient->cnic_hash)
        ->not->toBe(hash('sha256', '35202-3333333-1'))
        ->toBe(hash_hmac('sha256', '35202-3333333-1', (string) config('app.key')));
});

test('treatment record narrative and json clinical fields are encrypted at rest', function () {
    $record = TreatmentRecord::factory()->create([
        'chief_complaint' => 'Severe chest pain radiating to left arm',
        'diagnosis_text' => 'Suspected myocardial infarction',
        'examination_findings' => ['bp' => '160/100', 'note' => 'Diaphoretic, anxious'],
        'prescriptions' => [['drug_name' => 'Aspirin', 'dose' => '300mg']],
    ]);

    $raw = DB::table('treatment_records')->where('id', $record->id)->first([
        'chief_complaint', 'diagnosis_text', 'examination_findings', 'prescriptions',
    ]);

    expect($raw->chief_complaint)->not->toContain('chest pain')
        ->and($raw->diagnosis_text)->not->toContain('myocardial')
        ->and($raw->examination_findings)->not->toContain('Diaphoretic')
        ->and($raw->prescriptions)->not->toContain('Aspirin');

    $fresh = $record->fresh();
    expect($fresh->chief_complaint)->toBe('Severe chest pain radiating to left arm')
        ->and($fresh->diagnosis_text)->toBe('Suspected myocardial infarction')
        ->and($fresh->examination_findings)->toBe(['bp' => '160/100', 'note' => 'Diaphoretic, anxious'])
        ->and($fresh->prescriptions)->toBe([['drug_name' => 'Aspirin', 'dose' => '300mg']]);
});

test('birth certificate names and cnics are encrypted at rest', function () {
    $certificate = BirthCertificate::factory()->create([
        'child_name' => 'Ali Raza',
        'mother_name' => 'Fatima Raza',
        'mother_cnic' => '35202-4444444-1',
    ]);

    $raw = DB::table('birth_certificates')->where('id', $certificate->id)->first(['child_name', 'mother_name', 'mother_cnic']);

    expect($raw->child_name)->not->toBe('Ali Raza')
        ->and($raw->mother_name)->not->toBe('Fatima Raza')
        ->and($raw->mother_cnic)->not->toBe('35202-4444444-1');

    $fresh = $certificate->fresh();
    expect($fresh->child_name)->toBe('Ali Raza')
        ->and($fresh->mother_name)->toBe('Fatima Raza')
        ->and($fresh->mother_cnic)->toBe('35202-4444444-1');
});

test('death certificate informant details are encrypted at rest', function () {
    $certificate = DeathCertificate::factory()->create([
        'informant_name' => 'Muhammad Tariq',
        'informant_cnic' => '35202-5555555-2',
    ]);

    $raw = DB::table('death_certificates')->where('id', $certificate->id)->first(['informant_name', 'informant_cnic']);

    expect($raw->informant_name)->not->toBe('Muhammad Tariq')
        ->and($raw->informant_cnic)->not->toBe('35202-5555555-2');

    $fresh = $certificate->fresh();
    expect($fresh->informant_name)->toBe('Muhammad Tariq')
        ->and($fresh->informant_cnic)->toBe('35202-5555555-2');
});

test('referral certificate clinical notes are encrypted at rest', function () {
    $certificate = ReferralCertificate::factory()->create([
        'notes' => '<p>Patient stable for transfer, given aspirin 300mg.</p>',
    ]);

    $raw = DB::table('referral_certificates')->where('id', $certificate->id)->value('notes');

    expect($raw)->not->toContain('stable for transfer');
    expect($certificate->fresh()->notes)->toBe('<p>Patient stable for transfer, given aspirin 300mg.</p>');
});

test('patient version snapshots do not leak plaintext PII', function () {
    $patient = Patient::factory()->create(['cnic' => '35202-6666666-1', 'name' => 'Original Name']);

    $patient->update(['name' => 'Updated Name']);

    $version = PatientVersion::where('patient_id', $patient->id)->latest('id')->first();
    $rawSnapshot = DB::table('patient_versions')->where('id', $version->id)->value('snapshot');

    expect($rawSnapshot)->not->toContain('35202-6666666-1')
        ->and($rawSnapshot)->not->toContain('Original Name');

    expect($version->snapshot['cnic'])->toBe('35202-6666666-1')
        ->and($version->snapshot['name'])->toBe('Original Name');
});

test('treatment record version snapshots do not leak plaintext PHI', function () {
    $record = TreatmentRecord::factory()->create(['chief_complaint' => 'Original complaint text']);

    $record->update(['chief_complaint' => 'Updated complaint text']);

    $version = TreatmentRecordVersion::where('treatment_record_id', $record->id)->latest('id')->first();
    $rawSnapshot = DB::table('treatment_record_versions')->where('id', $version->id)->value('snapshot');

    expect($rawSnapshot)->not->toContain('Original complaint text');
    expect($version->snapshot['chief_complaint'])->toBe('Original complaint text');
});

test('version snapshot columns are widened and legacy plaintext snapshots get encrypted', function (string $table, string $foreignKey, Closure $makeParent) {
    $parentId = $makeParent()->id;

    $versionId = DB::table($table)->insertGetId([
        $foreignKey => $parentId,
        'snapshot' => json_encode(['cnic' => '35202-7777777-1']),
        'change_reason' => 'record_update',
        'changed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $migration = require database_path('migrations/2026_09_30_141636_widen_and_encrypt_version_snapshots.php');
    $migration->up();
    $migration->up();

    $rawSnapshot = DB::table($table)->where('id', $versionId)->value('snapshot');

    expect(Schema::getColumnType($table, 'snapshot'))->not->toBe('json')
        ->and($rawSnapshot)->not->toContain('35202-7777777-1')
        ->and(json_decode(Crypt::decryptString($rawSnapshot), true))->toBe(['cnic' => '35202-7777777-1']);
})->with([
    'patient' => ['patient_versions', 'patient_id', fn () => Patient::factory()->create()],
    'treatment record' => ['treatment_record_versions', 'treatment_record_id', fn () => TreatmentRecord::factory()->create()],
    'service order' => ['service_order_versions', 'service_order_id', fn () => ServiceOrder::factory()->create()],
]);

function insertPatientVersionSnapshot(string $snapshot): int
{
    return DB::table('patient_versions')->insertGetId([
        'patient_id' => Patient::factory()->create()->id,
        'snapshot' => $snapshot,
        'change_reason' => 'record_update',
        'changed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function versionSnapshotMigration(): object
{
    return require database_path('migrations/2026_09_30_141636_widen_and_encrypt_version_snapshots.php');
}

test('version snapshot migration never double-encrypts ciphertext from another key', function () {
    $foreignCiphertext = (new Encrypter(Encrypter::generateKey('aes-256-cbc'), 'aes-256-cbc'))
        ->encryptString(json_encode(['cnic' => '35202-8888888-1']));
    $versionId = insertPatientVersionSnapshot($foreignCiphertext);

    versionSnapshotMigration()->up();

    expect(DB::table('patient_versions')->where('id', $versionId)->value('snapshot'))->toBe($foreignCiphertext);
});

test('version snapshot migration rolls back to plain json snapshots', function () {
    $versionId = insertPatientVersionSnapshot(json_encode(['cnic' => '35202-9999999-1']));
    $migration = versionSnapshotMigration();

    $migration->up();
    $migration->down();

    expect(json_decode(DB::table('patient_versions')->where('id', $versionId)->value('snapshot'), true))
        ->toBe(['cnic' => '35202-9999999-1']);
});

test('version snapshot rollback aborts without changes when a snapshot cannot be decrypted', function () {
    $migration = versionSnapshotMigration();
    $readableId = insertPatientVersionSnapshot(json_encode(['cnic' => '35202-1111111-1']));
    $migration->up();
    $encryptedReadable = DB::table('patient_versions')->where('id', $readableId)->value('snapshot');

    $foreignCiphertext = (new Encrypter(Encrypter::generateKey('aes-256-cbc'), 'aes-256-cbc'))
        ->encryptString(json_encode(['cnic' => '35202-2222222-1']));
    insertPatientVersionSnapshot($foreignCiphertext);

    expect(fn () => $migration->down())->toThrow(RuntimeException::class, 'Cannot roll back');
    expect(DB::table('patient_versions')->where('id', $readableId)->value('snapshot'))->toBe($encryptedReadable);
});

test('service order notes_json is encrypted at rest', function () {
    $notes = [
        'complaint' => 'Severe pain',
        'diagnosis' => 'Observation pending',
    ];

    $serviceOrder = ServiceOrder::factory()->create([
        'notes_json' => $notes,
    ]);

    $rawNotes = DB::table('service_orders')->where('id', $serviceOrder->id)->value('notes_json');

    expect($rawNotes)->toBeString()
        ->and($rawNotes)->not->toContain('Severe pain')
        ->and($serviceOrder->fresh()->notes_json)->toBe($notes);
});
