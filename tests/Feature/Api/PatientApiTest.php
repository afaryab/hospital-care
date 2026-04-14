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
    Patient::factory()->create(['name' => 'Ahmad Farooq']);
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

    $patient = Patient::query()->latest('id')->first();

    expect($patient)->not->toBeNull()
        ->and($patient?->name)->toBe('Test Patient')
        ->and($patient?->contact)->toBe('03001234567');
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
    ])->assertStatus(409)
        ->assertJsonPath('warning', true)
        ->assertJsonPath('can_proceed', true);
});

test('patient update modifies patient record', function () {
    $patient = Patient::factory()->create();

    $this->postJson(route('api-patients-edit', $patient->id), [
        'contact' => '03009999999',
        'age' => 35,
        'gender' => 'm',
    ])->assertOk()
        ->assertJsonPath('message', 'Patient updated successfully');

    $patient->refresh();

    expect($patient->contact)->toBe('03009999999');
});

test('patient update validates required fields', function () {
    $patient = Patient::factory()->create();

    $this->postJson(route('api-patients-edit', $patient->id), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['contact', 'age']);
});

// ─── CNIC search (bug fix #5) ─────────────────────────────────────────────────

test('patient search ignores partial cnic shorter than 15 chars', function () {
    // CNIC is encrypted at rest; hash-based lookup only triggers at exactly 15 chars.
    // A partial CNIC input has no filtering effect — all patients remain in results.
    Patient::factory()->create(['cnic' => '35202-1234567-1', 'name' => 'Ahmad Ali']);
    Patient::factory()->create(['cnic' => '61101-9876543-2', 'name' => 'Zulfiqar Khan']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    // Both patients appear because the partial CNIC is ignored
    expect($names)->toContain('Ahmad Ali')
        ->and($names)->toContain('Zulfiqar Khan');
});

test('patient search returns exact match for full 15-char cnic', function () {
    Patient::factory()->create(['cnic' => '35202-1234567-1']);
    Patient::factory()->create(['cnic' => '35202-9999999-9']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202-1234567-1',
    ])->assertOk();

    $exact = $response->json('data.exact');
    $exactCnics = collect($exact)->pluck('cnic');
    expect($exactCnics)->toContain('35202-1234567-1');
});

test('partial cnic input does not exclude patients without cnic', function () {
    // CNIC is encrypted; partial input (< 15 chars) is ignored — no filtering occurs.
    // Both patients with and without a CNIC appear in results.
    Patient::factory()->create(['cnic' => '35202-1234567-1', 'name' => 'Ahmad Ali']);
    Patient::factory()->create(['cnic' => null, 'name' => 'No CNIC Patient']);

    $response = $this->postJson(route('api-patients-search'), [
        'cnic_number' => '35202',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    expect($names)->toContain('Ahmad Ali')
        ->and($names)->toContain('No CNIC Patient');
});

// ─── Name search ordering (prefix-first) ─────────────────────────────────────

test('patient name search returns prefix matches before contains matches', function () {
    Patient::factory()->create(['name' => 'Ahmad Farooq']);
    Patient::factory()->create(['name' => 'Muhammad Ahmad']);

    $response = $this->postJson(route('api-patients-search'), [
        'patient_name' => 'Ahmad',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name')->values()->all();

    // Prefix match ("Ahmad Farooq") should come before contains match ("Muhammad Ahmad")
    expect($names)->toContain('Ahmad Farooq')
        ->and($names)->toContain('Muhammad Ahmad')
        ->and(array_search('Ahmad Farooq', $names))->toBeLessThan(array_search('Muhammad Ahmad', $names));
});

// ─── Age search filter ───────────────────────────────────────────────────────

test('patient search filters out patients whose age is far outside the requested range', function () {
    // 25-year-old patient: age_days ≈ 25 * 365 = 9125
    Patient::factory()->create(['name' => 'Young Patient', 'age_days' => '9125']);
    // 80-year-old patient: age_days ≈ 80 * 365 = 29200
    Patient::factory()->create(['name' => 'Old Patient', 'age_days' => '29200']);

    $response = $this->postJson(route('api-patients-search'), [
        'patient_name' => 'Patient',
        'patient_age' => '25',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');

    // The 25-year-old is within ±10 years of 25
    expect($names)->toContain('Young Patient');
    // The 80-year-old is outside ±10 years of 25
    expect($names)->not->toContain('Old Patient');
});
