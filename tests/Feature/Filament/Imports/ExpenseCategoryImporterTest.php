<?php

use App\Filament\Imports\ExpenseCategoryImporter;
use App\Models\ExpenseCategory;

test('importing a new expense category creates it', function () {
    $import = makeFilamentImport(ExpenseCategoryImporter::class);
    $importer = new ExpenseCategoryImporter($import, [
        'name' => 'name',
        'type' => 'type',
        'pay_doc' => 'pay_doc',
    ], []);

    $importer([
        'name' => 'Pharmacy',
        'type' => 'OPD',
        'pay_doc' => '1',
    ]);

    $category = ExpenseCategory::where('name', 'Pharmacy')->first();
    expect($category)->not->toBeNull()
        ->and($category->type)->toBe('OPD')
        ->and($category->pay_doc)->toBeTrue();
});

test('re-importing the same name updates the existing expense category rather than duplicating it', function () {
    ExpenseCategory::factory()->create(['name' => 'Pharmacy', 'type' => 'OPD']);

    $import = makeFilamentImport(ExpenseCategoryImporter::class);
    $importer = new ExpenseCategoryImporter($import, [
        'name' => 'name',
        'type' => 'type',
    ], []);

    $importer([
        'name' => 'Pharmacy',
        'type' => 'LAB',
    ]);

    expect(ExpenseCategory::where('name', 'Pharmacy')->count())->toBe(1)
        ->and(ExpenseCategory::where('name', 'Pharmacy')->value('type'))->toBe('LAB');
});
