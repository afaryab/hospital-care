<?php

use App\Helpers\PiiHasher;
use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('creating patient with existing cnic returns a warning', function () {
    Patient::factory()->create([
        'cnic' => '35202-1234567-1',
        'contact' => '03001234567',
    ]);

    $this->postJson(route('api-patients-store'), [
        'name' => 'Duplicate CNIC',
        'cnic' => '35202-1234567-1',
        'contact' => '03111234567',
        'gender' => 'm',
    ])
        ->assertStatus(409)
        ->assertJsonPath('warning', true)
        ->assertJsonPath('can_proceed', true);
});

test('creating patient with existing contact returns a warning', function () {
    Patient::factory()->create([
        'cnic' => '35202-1111111-1',
        'contact' => '03009998888',
    ]);

    $this->postJson(route('api-patients-store'), [
        'name' => 'Duplicate Contact',
        'cnic' => '35202-2222222-2',
        'contact' => '03009998888',
        'gender' => 'f',
    ])
        ->assertStatus(409)
        ->assertJsonPath('warning', true)
        ->assertJsonPath('can_proceed', true);
});

test('receptionist can proceed and create despite duplicate warning', function () {
    Patient::factory()->create([
        'cnic' => '35202-3333333-1',
        'contact' => '03001112222',
    ]);

    $this->postJson(route('api-patients-store'), [
        'name' => 'Force Create Patient',
        'cnic' => '35202-3333333-1',
        'contact' => '03110001111',
        'gender' => 'm',
        'force_create' => true,
    ])
        ->assertStatus(201)
        ->assertJsonPath('message', 'Patient created successfully');

    expect(Patient::query()->where('name', 'Force Create Patient')->exists())->toBeTrue();
});

test('receptionist can select existing patient to avoid duplicate creation', function () {
    $existingPatient = Patient::factory()->create([
        'cnic' => '35202-4444444-1',
        'contact' => '03007776666',
    ]);

    $response = $this->postJson(route('api-patients-store'), [
        'name' => 'Existing Selector',
        'cnic' => '35202-4444444-1',
        'contact' => '03007776666',
        'gender' => 'f',
        'selected_patient_id' => $existingPatient->id,
    ])
        ->assertOk()
        ->assertJsonPath('used_existing', true)
        ->assertJsonPath('data.id', $existingPatient->id);

    expect(Patient::query()->where('cnic_hash', $existingPatient->cnic_hash)->count())->toBe(1);
});

test('cnic duplicate check works with encrypted data via cnic hash', function () {
    $existingPatient = Patient::factory()->create([
        'cnic' => '35202-5555555-1',
        'contact' => '03005556666',
    ]);

    $this->postJson(route('api-patients-store'), [
        'name' => 'Encrypted Duplicate Check',
        'cnic' => '35202-5555555-1',
        'contact' => '03115556666',
        'gender' => 't',
    ])
        ->assertStatus(409)
        ->assertJsonPath('warning', true);

    expect($existingPatient->cnic_hash)->toBe(PiiHasher::cnic('35202-5555555-1'));
});
