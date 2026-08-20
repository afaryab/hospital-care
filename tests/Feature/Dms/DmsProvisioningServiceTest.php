<?php

use App\Models\Patient;
use App\Models\User;
use App\Services\Dms\DmsProvisioningService;

beforeEach(function () {
    $this->provisioning = app(DmsProvisioningService::class);
});

test('patientFolder is idempotent and named after the ps_number', function () {
    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/03/0007']);

    $first = $this->provisioning->patientFolder($patient);
    $second = $this->provisioning->patientFolder($patient);

    expect($first->id)->toBe($second->id)
        ->and($first->name)->toBe('PS/2026/03/0007')
        ->and($first->is_system)->toBeTrue()
        ->and($first->owner_type)->toBe(Patient::class)
        ->and($first->owner_id)->toBe($patient->id)
        ->and($first->parent->name)->toBe('Patients');
});

test('doctorFolder is idempotent and keyed off the user id', function () {
    $doctor = User::factory()->create(['name' => 'Dr Jane Doe']);

    $first = $this->provisioning->doctorFolder($doctor);
    $second = $this->provisioning->doctorFolder($doctor);

    expect($first->id)->toBe($second->id)
        ->and($first->owner_type)->toBe(User::class)
        ->and($first->owner_id)->toBe($doctor->id)
        ->and($first->parent->name)->toBe('Doctors');
});

test('different patients get different folders under the same Patients root', function () {
    $a = Patient::factory()->create(['ps_number' => 'PS/2026/03/0001']);
    $b = Patient::factory()->create(['ps_number' => 'PS/2026/03/0002']);

    $folderA = $this->provisioning->patientFolder($a);
    $folderB = $this->provisioning->patientFolder($b);

    expect($folderA->id)->not->toBe($folderB->id)
        ->and($folderA->parent_id)->toBe($folderB->parent_id);
});
