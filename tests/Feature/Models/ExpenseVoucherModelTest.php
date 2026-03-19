<?php

use App\Models\ExpenseVoucher;

test('expense voucher status is pending when no transaction linked', function () {
    $voucher = ExpenseVoucher::factory()->make([
        'transaction_id' => null,
        'transaction_element_id' => null,
    ]);

    expect($voucher->status)->toBe('pending');
});

test('expense voucher status is payed when transaction is linked', function () {
    $voucher = ExpenseVoucher::factory()->make([
        'transaction_id' => 1,
        'transaction_element_id' => 1,
    ]);

    expect($voucher->status)->toBe('payed');
});

test('expense voucher auto-generates vc_number on create', function () {
    $voucher = ExpenseVoucher::factory()->create(['vc_number' => null]);

    expect($voucher->vc_number)->not->toBeNull();
    expect($voucher->vc_number)->toStartWith('VC/');
});

test('expense voucher vc_number format is correct', function () {
    $voucher = ExpenseVoucher::factory()->create(['vc_number' => null]);
    $now = now();

    expect($voucher->vc_number)->toStartWith('VC/' . $now->format('Y') . '/' . $now->format('m') . '/');
    expect(explode('/', $voucher->vc_number))->toHaveCount(4);
});

test('expense voucher does not overwrite existing vc_number', function () {
    $voucher = ExpenseVoucher::factory()->create(['vc_number' => 'VC/2026/03/9999']);

    expect($voucher->vc_number)->toBe('VC/2026/03/9999');
});

test('expense voucher vc_number increments sequentially', function () {
    $first = ExpenseVoucher::factory()->create(['vc_number' => null]);
    $second = ExpenseVoucher::factory()->create(['vc_number' => null]);

    $firstSeq = (int) explode('/', $first->vc_number)[3];
    $secondSeq = (int) explode('/', $second->vc_number)[3];

    expect($secondSeq)->toBeGreaterThan($firstSeq);
});

test('expense voucher belongs to expense category', function () {
    $voucher = ExpenseVoucher::factory()->create();

    expect($voucher->expCategory())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
