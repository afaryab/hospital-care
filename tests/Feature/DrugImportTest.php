<?php

use App\Imports\DrugImport;
use App\Models\Drug;
use App\Models\DrugCategory;
use Illuminate\Support\Collection;

test('import creates drugs and resolves categories', function () {
    $import = new DrugImport;

    $import->collection(new Collection([
        [
            'name' => 'Amoxicillin',
            'generic_name' => 'Amoxicillin Trihydrate',
            'type' => 'Capsule',
            'category' => 'Antibiotics',
            'strength' => '500mg',
            'default_dose' => '1 capsule',
            'default_frequency' => 'TDS',
            'default_duration' => '7 days',
            'default_route' => 'Oral',
        ],
    ]));

    expect($import->imported)->toBe(1);
    expect($import->skipped)->toBe(0);

    $category = DrugCategory::where('name', 'Antibiotics')->first();
    expect($category)->not->toBeNull();

    $this->assertDatabaseHas(Drug::class, [
        'name' => 'Amoxicillin',
        'generic_name' => 'Amoxicillin Trihydrate',
        'strength' => '500mg',
        'drug_category_id' => $category->id,
        'is_active' => 1,
    ]);
});

test('import skips rows without a name', function () {
    $import = new DrugImport;

    $import->collection(new Collection([
        ['name' => '', 'generic_name' => 'Something'],
        ['name' => '   ', 'generic_name' => 'Something Else'],
    ]));

    expect($import->imported)->toBe(0);
    expect($import->skipped)->toBe(2);
    expect(Drug::count())->toBe(0);
});

test('import upserts on name and generic name instead of duplicating', function () {
    $import = new DrugImport;

    $row = [
        'name' => 'Panadol',
        'generic_name' => 'Paracetamol',
        'strength' => '500mg',
    ];

    $import->collection(new Collection([$row]));
    $import->collection(new Collection([array_merge($row, ['strength' => '650mg'])]));

    expect(Drug::count())->toBe(1);
    $this->assertDatabaseHas(Drug::class, [
        'name' => 'Panadol',
        'generic_name' => 'Paracetamol',
        'strength' => '650mg',
    ]);
});

test('import reuses an existing category instead of creating a duplicate', function () {
    $category = DrugCategory::factory()->create(['name' => 'Analgesics']);

    $import = new DrugImport;
    $import->collection(new Collection([
        ['name' => 'Ibuprofen', 'category' => 'analgesics'],
        ['name' => 'Diclofenac', 'category' => 'Analgesics'],
    ]));

    expect(DrugCategory::count())->toBe(1);
    expect(Drug::where('drug_category_id', $category->id)->count())->toBe(2);
});

test('import supports legacy column aliases like salt and dose', function () {
    $import = new DrugImport;

    $import->collection(new Collection([
        [
            'name' => 'Metformin',
            'salt' => 'Metformin Hydrochloride',
            'dose' => '1 tablet',
            'frequency' => 'BD',
            'duration' => '30 days',
            'route' => 'Oral',
            'usage' => 'Take with food',
        ],
    ]));

    $this->assertDatabaseHas(Drug::class, [
        'name' => 'Metformin',
        'generic_name' => 'Metformin Hydrochloride',
        'default_dose' => '1 tablet',
        'default_frequency' => 'BD',
        'default_duration' => '30 days',
        'default_route' => 'Oral',
        'usage_instructions' => 'Take with food',
    ]);
});
