<?php

use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('patient search returns json with data structure', function () {
    Patient::factory()->count(3)->create();

    $this->postJson(route('api-patients-search'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('patient search filters by patient name', function () {
    $patient = Patient::factory()->create(['name' => 'Ahmad Farooq']);
    Patient::factory()->create(['name' => 'Zulfiqar Khan']);

    $response = $this->postJson(route('api-patients-search'), [
        'patient_name' => 'Ahmad',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    expect($names)->toContain('Ahmad Farooq');
});

test('patient search filters by mr_number prefix', function () {
    $patient = Patient::factory()->withPsNumber('PS/2026/03/0001')->create();
    Patient::factory()->withPsNumber('PS/2025/01/0001')->create();

    $response = $this->postJson(route('api-patients-search'), [
        'mr_number' => 'PS/2026',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $psNumbers = collect($possible)->pluck('ps_number');
    expect($psNumbers)->toContain('PS/2026/03/0001');
});

test('patient create stores a new patient in database', function () {
    $this->postJson(route('api-patients-store'), [
        'name' => 'Test Patient',
        'contact' => '03001234567',
        'gender' => 'm',
    ])->assertStatus(201)
        ->assertJsonPath('message', 'Patient created successfully');

    $this->assertDatabaseHas('patients', ['name' => 'Test Patient', 'contact' => '03001234567']);
});

test('patient create validates required fields', function () {
    $this->postJson(route('api-patients-store'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'contact', 'gender']);
});

test('patient create validates gender is in allowed values', function () {
    $this->postJson(route('api-patients-store'), [
        'name' => 'Test',
        'contact' => '03001234567',
        'gender' => 'x',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['gender']);
});

test('patient create validates cnic uniqueness', function () {
    Patient::factory()->create(['cnic' => '12345-1234567-1']);

    $this->postJson(route('api-patients-store'), [
        'name' => 'Another Patient',
        'contact' => '03001111111',
        'gender' => 'f',
        'cnic' => '12345-1234567-1',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['cnic']);
});

test('patient update modifies patient record', function () {
    $patient = Patient::factory()->create();

    $this->postJson(route('api-patients-edit', $patient->id), [
        'contact' => '03009999999',
        'age' => 35,
        'gender' => 'm',
    ])->assertOk()
        ->assertJsonPath('message', 'Patient updated successfully');

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'contact' => '03009999999',
    ]);
});

test('patient update validates required fields', function () {
    $patient = Patient::factory()->create();

    $this->postJson(route('api-patients-edit', $patient->id), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['contact', 'age']);
});


// ─── CNIC search (bug fix #5) ─────────────────────────────────────────────────

test('patient search filters by cnic prefix', function () {
    Patient::factory()->create(['cnic' => '35202-1234567-1', 'name' => 'Ahmad Ali']);
    Patient::factory()->create(['cnic' => '61101-9876543-2', 'name' => 'Zulfiqar Khan']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $cnics = collect($possible)->pluck('cnic');
    expect($cnics)->toContain('35202-1234567-1')
        ->and($cnics)->not->toContain('61101-9876543-2');
});

test('patient search returns exact match for full 15-char cnic', function () {
    $target = Patient::factory()->create(['cnic' => '35202-1234567-1']);
    Patient::factory()->create(['cnic' => '35202-9999999-9']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202-1234567-1',
    ])->assertOk();

    $exact = $response->json('data.exact');
    $exactCnics = collect($exact)->pluck('cnic');
    expect($exactCnics)->toContain('35202-1234567-1');
});

test('cnic search does not return results for unrelated mr_number', function () {
    // Regression: before the fix, cnic_number search used $mrNumber (always false
    // when mr_number was not provided), causing the LIKE query to match everything.
    Patient::factory()->create(['cnic' => '35202-1234567-1', 'name' => 'Ahmad Ali']);
    Patient::factory()->create(['cnic' => null, 'name' => 'No CNIC Patient']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    expect($names)->toContain('Ahmad Ali')
        ->and($names)->not->toContain('No CNIC Patient');
});
