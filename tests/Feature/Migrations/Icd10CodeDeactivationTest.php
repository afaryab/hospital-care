<?php

use App\Models\Icd10Code;

test('non-WHO legacy ICD-10 codes are deactivated by the migration', function () {
    $nonWhoCodes = [
        'I25.10', 'K40.90', 'K57.30', 'K80.20', 'N30.00', 'S62.00', 'S72.00',
        'S82.00', 'Z00.00', 'Z34.00', 'Z34.90', 'Z38.00',
        'E11.65', 'R07.9', 'R10.9', 'R53.1', 'R73.09', 'Z00.01',
    ];

    $codes = Icd10Code::whereIn('code', $nonWhoCodes)->get(['code', 'is_active']);

    expect($codes)->toHaveCount(count($nonWhoCodes));
    $codes->each(fn (Icd10Code $code) => expect($code->is_active)->toBeFalse("{$code->code} should be deactivated"));
});

test('legitimate WHO codes affected by the importer Modifier-expansion gap are left active', function () {
    // These looked "missing from WHO" on a naive cross-check but are real
    // WHO codes defined via a shared <Modifier> block the importer doesn't
    // yet expand — must not be caught up in the deactivation.
    $stillActive = ['E10.9', 'E11.9', 'E14.9', 'F10.2', 'K25.9', 'K27.9'];

    Icd10Code::whereIn('code', $stillActive)->get(['code', 'is_active'])
        ->each(fn (Icd10Code $code) => expect($code->is_active)->toBeTrue("{$code->code} should remain active"));
});
