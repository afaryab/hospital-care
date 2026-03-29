<?php

use App\Models\Patient;
use App\Models\PatientVersion;
use App\Models\TreatmentRecord;
use Illuminate\Validation\ValidationException;

test('editing a patient creates a version record', function () {
    $patient = Patient::factory()->create([
        'name' => 'Initial Name',
    ]);

    $patient->update([
        'name' => 'Updated Name',
    ]);

    expect(PatientVersion::query()->where('patient_id', $patient->id)->count())->toBe(1);
});

test('original patient data is preserved in version snapshot', function () {
    $patient = Patient::factory()->create([
        'name' => 'Original Name',
    ]);

    $patient->update([
        'name' => 'Changed Name',
    ]);

    $version = PatientVersion::query()
        ->where('patient_id', $patient->id)
        ->latest('id')
        ->first();

    expect($version)->not->toBeNull()
        ->and($version->snapshot['name'] ?? null)->toBe('Original Name')
        ->and($patient->fresh()->name)->toBe('Changed Name');
});

test('editing a treatment record after finalization is rejected', function () {
    $treatmentRecord = TreatmentRecord::factory()->finalized()->create([
        'diagnosis_text' => 'Initial diagnosis',
    ]);

    $this->expectException(ValidationException::class);

    $treatmentRecord->update([
        'diagnosis_text' => 'Changed diagnosis',
    ]);
});

test('soft deleted patient is not actually removed from database', function () {
    $patient = Patient::factory()->create();

    $patient->delete();

    $this->assertSoftDeleted('patients', ['id' => $patient->id]);
});

test('hard delete on patient is prevented', function () {
    $patient = Patient::factory()->create();

    $this->expectException(RuntimeException::class);

    $patient->forceDelete();
});

test('patient version history is retrievable', function () {
    $patient = Patient::factory()->create([
        'name' => 'Version 1',
    ]);

    $patient->update(['name' => 'Version 2']);
    $patient->update(['name' => 'Version 3']);

    $history = $patient->fresh()->versions;

    expect($history)->toHaveCount(2)
        ->and($history->first()->snapshot['name'] ?? null)->toBe('Version 2')
        ->and($history->last()->snapshot['name'] ?? null)->toBe('Version 1');
});
