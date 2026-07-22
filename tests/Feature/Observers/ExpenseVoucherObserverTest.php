<?php

use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('expense voucher vc_number is auto-generated when not provided', function () {
    $category = ExpenseCategory::factory()->create();
    $user = User::factory()->create();

    $voucher = ExpenseVoucher::create([
        'exp_category_id' => $category->id,
        'payed_to' => $user->id,
        'payed_to_name' => 'Test Vendor',
        'amount' => 500.00,
    ]);

    expect($voucher->vc_number)->not->toBeNull();
    expect($voucher->vc_number)->toMatch('/^VC\/\d{4}\/\d{2}\/\d{4}$/');
});

test('expense voucher vc_number format contains current year and month', function () {
    $category = ExpenseCategory::factory()->create();
    $user = User::factory()->create();

    $voucher = ExpenseVoucher::create([
        'exp_category_id' => $category->id,
        'payed_to' => $user->id,
        'payed_to_name' => 'Test Vendor',
        'amount' => 1000.00,
    ]);

    $year = now()->format('Y');
    $month = now()->format('m');

    expect($voucher->vc_number)->toContain("VC/{$year}/{$month}/");
});

test('expense voucher vc_number is not overwritten when explicitly provided', function () {
    $voucher = ExpenseVoucher::factory()->create(['vc_number' => 'VC/2023/01/9999']);

    expect($voucher->vc_number)->toBe('VC/2023/01/9999');
});

test('expense voucher vc_number cannot be changed after creation', function () {
    $voucher = ExpenseVoucher::factory()->create();
    $original = $voucher->vc_number;

    $voucher->vc_number = 'VC/0000/00/0000';
    $voucher->save();
    $voucher->refresh();

    expect($voucher->vc_number)->toBe($original);
});

test('expense voucher edited_amount is stored when amount is changed', function () {
    $voucher = ExpenseVoucher::factory()->create(['amount' => 1000.00]);
    $originalAmount = (float) $voucher->amount;

    $voucher->amount = 2000.00;
    $voucher->save();
    $voucher->refresh();

    expect((float) $voucher->edited_amount)->toBe($originalAmount);
    expect((float) $voucher->amount)->toBe(2000.00);
});

test('expense voucher vc_numbers are sequential when auto-generated', function () {
    $category = ExpenseCategory::factory()->create();
    $user = User::factory()->create();

    $first = ExpenseVoucher::create([
        'exp_category_id' => $category->id,
        'payed_to' => $user->id,
        'payed_to_name' => 'Vendor One',
        'amount' => 100.00,
    ]);

    $second = ExpenseVoucher::create([
        'exp_category_id' => $category->id,
        'payed_to' => $user->id,
        'payed_to_name' => 'Vendor Two',
        'amount' => 200.00,
    ]);

    [$firstNum] = sscanf($first->vc_number, 'VC/%*d/%*d/%d');
    [$secondNum] = sscanf($second->vc_number, 'VC/%*d/%*d/%d');

    expect($secondNum)->toBe($firstNum + 1);
});

test('expense voucher belongs to expense category relationship', function () {
    $voucher = ExpenseVoucher::factory()->create();

    expect($voucher->expCategory())->toBeInstanceOf(BelongsTo::class);
});
