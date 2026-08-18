<?php

use App\Models\Icd10Code;

test('icd10:import imports codes from the given path', function () {
    $this->artisan('icd10:import', ['path' => base_path('tests/Fixtures/icd10/claml-sample.xml')])
        ->assertSuccessful();

    expect(Icd10Code::whereIn('code', ['A00', 'A00.0', 'C00', 'C00.0', 'K77.0'])->count())->toBe(5);
});

test('icd10:import defaults to the bundled WHO ClaML file when no path is given', function () {
    $this->artisan('icd10:import')->assertSuccessful();

    expect(Icd10Code::count())->toBeGreaterThan(10000);
});

test('icd10:import fails cleanly when the file does not exist', function () {
    $this->artisan('icd10:import', ['path' => base_path('tests/Fixtures/icd10/does-not-exist.xml')])
        ->assertFailed();
});
