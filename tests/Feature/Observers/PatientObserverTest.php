<?php

use App\Models\Patient;

test('patient ps_number is auto-generated on create', function () {
    $patient = Patient::factory()->create();

    expect($patient->ps_number)->not->toBeNull();
    expect($patient->ps_number)->toMatch('/^PS\/\d{4}\/\d{2}\/\d{4}$/');
});

test('patient ps_number format contains current year and month', function () {
    $patient = Patient::factory()->create();

    $year = now()->format('Y');
    $month = now()->format('m');

    expect($patient->ps_number)->toContain("PS/{$year}/{$month}/");
});

test('patient ps_number is not overwritten when explicitly provided', function () {
    $patient = Patient::factory()->withPsNumber('PS/2023/01/0099')->create();

    expect($patient->ps_number)->toBe('PS/2023/01/0099');
});

test('patient ps_numbers are sequential', function () {
    $first = Patient::factory()->create();
    $second = Patient::factory()->create();

    [$firstNum] = sscanf($first->ps_number, 'PS/%*d/%*d/%d');
    [$secondNum] = sscanf($second->ps_number, 'PS/%*d/%*d/%d');

    expect($secondNum)->toBe($firstNum + 1);
});

test('patient ps_number parts attribute is accessible after creation', function () {
    $patient = Patient::factory()->create();

    $parts = $patient->ps_number_parts;

    expect($parts)->toHaveKeys(['year', 'month', 'number']);
    expect($parts['year'])->not->toBeNull();
    expect($parts['month'])->not->toBeNull();
    expect($parts['number'])->not->toBeNull();
});
