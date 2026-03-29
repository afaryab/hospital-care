<?php

use App\Models\ExpenseCategory;

test('expense category can be created with factory', function () {
    $category = ExpenseCategory::factory()->create();

    expect($category)->toBeInstanceOf(ExpenseCategory::class)
        ->and($category->name)->not->toBeNull()
        ->and($category->pay_doc)->toBeFalse()
        ->and($category->pay_others)->toBeFalse()
        ->and($category->pay_users)->toBeFalse()
        ->and($category->pay_patient)->toBeFalse()
        ->and($category->allow_petty_cash)->toBeTrue()
        ->and($category->allow_voucher)->toBeTrue();
});

test('expense category boolean fields are cast correctly', function () {
    $category = ExpenseCategory::factory()->create([
        'pay_doc' => true,
        'pay_others' => true,
        'pay_users' => true,
        'pay_patient' => true,
        'allow_petty_cash' => false,
        'allow_voucher' => false,
    ]);

    expect($category->pay_doc)->toBeBool()->toBeTrue()
        ->and($category->pay_others)->toBeBool()->toBeTrue()
        ->and($category->pay_users)->toBeBool()->toBeTrue()
        ->and($category->pay_patient)->toBeBool()->toBeTrue()
        ->and($category->allow_petty_cash)->toBeBool()->toBeFalse()
        ->and($category->allow_voucher)->toBeBool()->toBeFalse();
});

test('expense category boolean fields default to false', function () {
    $category = ExpenseCategory::factory()->create([
        'pay_doc' => false,
        'pay_others' => false,
        'pay_users' => false,
    ]);

    expect($category->pay_doc)->toBeFalse()
        ->and($category->pay_others)->toBeFalse()
        ->and($category->pay_users)->toBeFalse();
});

test('expense category can be created with all fillable attributes', function () {
    $category = ExpenseCategory::create([
        'name' => 'Medical Supplies',
        'type' => 'OPD',
        'pay_doc' => true,
        'pay_others' => false,
        'pay_users' => false,
        'pay_patient' => false,
        'allow_petty_cash' => true,
        'allow_voucher' => true,
    ]);

    expect($category->name)->toBe('Medical Supplies')
        ->and($category->type)->toBe('OPD')
        ->and($category->pay_doc)->toBeTrue()
        ->and($category->allow_petty_cash)->toBeTrue()
        ->and($category->allow_voucher)->toBeTrue();
});

test('expense category can be updated', function () {
    $category = ExpenseCategory::factory()->create(['name' => 'Original Name']);

    $category->update(['name' => 'Updated Name', 'pay_doc' => true]);

    $category->refresh();

    expect($category->name)->toBe('Updated Name')
        ->and($category->pay_doc)->toBeTrue();
});

test('expense category can be deleted', function () {
    $category = ExpenseCategory::factory()->create();
    $id = $category->id;

    $category->delete();

    expect(ExpenseCategory::find($id))->toBeNull();
});
