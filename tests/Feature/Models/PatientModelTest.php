<?php

use App\Models\Patient;

test('patient ps_number_parts parses correctly', function () {
    $patient = Patient::factory()->withPsNumber('PS/2026/03/0001')->make();

    expect($patient->ps_number_parts)->toBe([
        'year' => '2026',
        'month' => '03',
        'number' => '0001',
    ]);
});

test('patient ps_number_parts returns null when ps_number is empty', function () {
    $patient = Patient::factory()->make(['ps_number' => null]);

    expect($patient->ps_number_parts)->toBeNull();
});

test('patient year month number attributes derive from ps_number', function () {
    $patient = Patient::factory()->withPsNumber('PS/2026/03/0042')->make();

    expect($patient->year)->toBe('2026')
        ->and($patient->month)->toBe('03')
        ->and($patient->number)->toBe('0042');
});

test('patient age attribute calculates from age_dob', function () {
    $patient = Patient::factory()->make([
        'age_dob' => now()->subYears(30)->toDateString(),
        'age_days' => null,
    ]);

    expect($patient->age)->toBe(30);
});

test('patient age attribute calculates from age_days when age_dob is null', function () {
    $patient = Patient::factory()->make([
        'age_dob' => null,
        'age_days' => 365 * 25,
    ]);

    expect($patient->age)->toBeGreaterThanOrEqual(24)
        ->and($patient->age)->toBeLessThanOrEqual(26);
});

test('patient age attribute returns null when no age data', function () {
    $patient = Patient::factory()->make([
        'age_dob' => null,
        'age_days' => null,
    ]);

    expect($patient->age)->toBeNull();
});

test('patient generateCounterNumber returns correctly formatted ps number', function () {
    $psNumber = Patient::generateCounterNumber();
    $now = now();

    expect($psNumber)->toStartWith('PS/'.$now->format('Y').'/'.$now->format('m').'/');
    expect(explode('/', $psNumber))->toHaveCount(4);
    expect(strlen(explode('/', $psNumber)[3]))->toBe(4);
});

test('patient generateCounterNumber increments correctly', function () {
    $first = Patient::generateCounterNumber();
    Patient::factory()->withPsNumber($first)->create();
    $second = Patient::generateCounterNumber();

    $firstSeq = (int) explode('/', $first)[3];
    $secondSeq = (int) explode('/', $second)[3];

    expect($secondSeq)->toBe($firstSeq + 1);
});

test('patient has many transactions relationship', function () {
    $patient = Patient::factory()->create();

    expect($patient->transactions())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('patient has many treatments relationship', function () {
    $patient = Patient::factory()->create();

    expect($patient->treatments())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});
