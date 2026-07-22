<?php

use App\Models\ExpenseVoucher;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;

test('a voucher shared across multiple service orders divides its amount evenly', function () {
    $voucher = ExpenseVoucher::factory()->create(['amount' => 1000]);
    $orders = ServiceOrder::factory()->count(4)->create();

    $voucher->serviceOrders()->attach($orders->pluck('id'));

    $totals = ServiceOrder::query()
        ->whereIn('id', $orders->pluck('id'))
        ->withVoucherExpenseTotal()
        ->get()
        ->pluck('voucher_expense_total', 'id');

    foreach ($orders as $order) {
        expect((float) $totals[$order->id])->toBe(250.0);
    }
});

test('a voucher linked to a single service order counts its full amount', function () {
    $voucher = ExpenseVoucher::factory()->create(['amount' => 500]);
    $order = ServiceOrder::factory()->create();

    $voucher->serviceOrders()->attach($order->id);

    $total = ServiceOrder::query()->withVoucherExpenseTotal()->find($order->id)->voucher_expense_total;

    expect((float) $total)->toBe(500.0);
});

test('a service order with no linked vouchers has a zero total', function () {
    $order = ServiceOrder::factory()->create();

    $total = ServiceOrder::query()->withVoucherExpenseTotal()->find($order->id)->voucher_expense_total;

    expect((float) $total)->toBe(0.0);
});

test('the voucher constraint filters which vouchers are summed without affecting the shared divisor', function () {
    // ExpenseVoucher::status is a computed accessor (payed = has transaction_id
    // + transaction_element_id), not a real column, so the constraint filters
    // on the underlying columns.
    $paidVoucher = ExpenseVoucher::factory()->create([
        'amount' => 1000,
        'transaction_id' => Transaction::factory(),
        'transaction_element_id' => TransactionElement::factory(),
    ]);
    $pendingVoucher = ExpenseVoucher::factory()->create(['amount' => 400]);
    $orders = ServiceOrder::factory()->count(2)->create();

    $paidVoucher->serviceOrders()->attach($orders->pluck('id'));
    $pendingVoucher->serviceOrders()->attach($orders->first()->id);

    $order = ServiceOrder::query()
        ->withVoucherExpenseTotal('paid_total', fn ($q) => $q->whereNotNull('expense_vouchers.transaction_id')->whereNotNull('expense_vouchers.transaction_element_id'))
        ->find($orders->first()->id);

    // Only the paid voucher counts toward paid_total, but its share is still
    // divided by all 2 orders it's linked to (not just the ones matching the constraint).
    expect((float) $order->paid_total)->toBe(500.0);
});
