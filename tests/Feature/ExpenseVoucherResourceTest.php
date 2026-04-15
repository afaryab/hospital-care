<?php

use App\Models\Administrator;
use App\Models\ExpenseVoucher;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

test('expense voucher status is pending when no transaction linked', function () {
    $voucher = ExpenseVoucher::factory()->create();

    expect($voucher->status)->toBe('pending');
});

test('expense voucher status is payed when transaction linked', function () {
    $voucher = ExpenseVoucher::factory()->create();
    $transaction = Transaction::factory()->expense()->create();
    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => 'VOUCHER_PAY',
        'income_or_expense' => 'EXPENSE',
        'exp_voucher_id' => $voucher->id,
    ]);

    $voucher->update([
        'transaction_id' => $transaction->id,
        'transaction_element_id' => $element->id,
    ]);

    $voucher->refresh();
    expect($voucher->status)->toBe('payed');
});

test('expense voucher transaction relationship returns linked transaction', function () {
    $voucher = ExpenseVoucher::factory()->create();
    $transaction = Transaction::factory()->expense()->create();
    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => 'VOUCHER_PAY',
        'income_or_expense' => 'EXPENSE',
        'exp_voucher_id' => $voucher->id,
    ]);

    $voucher->update([
        'transaction_id' => $transaction->id,
        'transaction_element_id' => $element->id,
    ]);

    $voucher->refresh();
    expect($voucher->transaction)->not->toBeNull()
        ->and($voucher->transaction->id)->toBe($transaction->id)
        ->and($voucher->transactionElement->id)->toBe($element->id);
});

test('expense voucher admin list page requires authentication', function () {
    $this->get('/admin/expense-vouchers')->assertRedirect();
});

test('admin user can access expense voucher list page', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'full']);

    $this->actingAs($user);
    $this->get('/admin/expense-vouchers')->assertSuccessful();
});

test('admin user can view a paid expense voucher', function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'full']);

    $this->actingAs($user);

    $voucher = ExpenseVoucher::factory()->create();
    $transaction = Transaction::factory()->expense()->create();
    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => 'VOUCHER_PAY',
        'income_or_expense' => 'EXPENSE',
        'exp_voucher_id' => $voucher->id,
    ]);

    $voucher->update([
        'transaction_id' => $transaction->id,
        'transaction_element_id' => $element->id,
    ]);

    $this->get("/admin/expense-vouchers/{$voucher->id}")->assertSuccessful();
});
