<?php

use App\Models\Icd10Code;
use App\Services\Icd10\ClaMlIcd10Importer;

beforeEach(function () {
    $this->fixture = base_path('tests/Fixtures/icd10/claml-sample.xml');
    // The 2026_05_16 migration seeds ~159 hand-picked codes on every fresh
    // test database, so assertions below scope to the fixture's own codes
    // rather than the table's total row count.
    $this->fixtureCodes = ['A00', 'A00.0', 'C00', 'C00.0', 'K77.0'];
});

test('import parses chapters, blocks, and categories and upserts only well-described categories', function () {
    $stats = (new ClaMlIcd10Importer)->import($this->fixture);

    expect($stats)->toBe([
        'chapters' => 2,
        'blocks' => 2,
        'categories' => 6,
        'imported' => 5,
        'skipped' => 1,
    ]);

    expect(Icd10Code::whereIn('code', $this->fixtureCodes)->count())->toBe(5);
});

test('import resolves each category to its chapter title via the SuperClass chain', function () {
    (new ClaMlIcd10Importer)->import($this->fixture);

    expect(Icd10Code::where('code', 'A00')->value('category'))
        ->toBe('Certain infectious and parasitic diseases')
        ->and(Icd10Code::where('code', 'A00.0')->value('category'))
        ->toBe('Certain infectious and parasitic diseases')
        ->and(Icd10Code::where('code', 'C00')->value('category'))
        ->toBe('Neoplasms');
});

test('import prefers the preferredLong rubric over preferred when both exist', function () {
    (new ClaMlIcd10Importer)->import($this->fixture);

    expect(Icd10Code::where('code', 'C00.0')->value('description'))
        ->toBe('Malignant neoplasm: External upper lip');
});

test('import strips the dagger/asterisk Reference cross-reference from the description', function () {
    (new ClaMlIcd10Importer)->import($this->fixture);

    expect(Icd10Code::where('code', 'K77.0')->value('description'))
        ->toBe('Amoebic liver abscess');
});

test('import skips categories with no preferred or preferredLong rubric', function () {
    (new ClaMlIcd10Importer)->import($this->fixture);

    expect(Icd10Code::where('code', 'Z99')->exists())->toBeFalse();
});

test('re-importing is idempotent and does not duplicate rows', function () {
    $importer = new ClaMlIcd10Importer;

    $importer->import($this->fixture);
    $importer->import($this->fixture);

    expect(Icd10Code::whereIn('code', $this->fixtureCodes)->count())->toBe(5)
        ->and(Icd10Code::where('code', 'A00')->count())->toBe(1);
});

test('re-importing does not reactivate a code an admin has manually deactivated', function () {
    $importer = new ClaMlIcd10Importer;
    $importer->import($this->fixture);

    Icd10Code::where('code', 'A00')->update(['is_active' => false]);

    $importer->import($this->fixture);

    expect(Icd10Code::where('code', 'A00')->value('is_active'))->toBeFalse();
});

test('import throws when the file does not exist', function () {
    (new ClaMlIcd10Importer)->import(base_path('tests/Fixtures/icd10/does-not-exist.xml'));
})->throws(RuntimeException::class);
