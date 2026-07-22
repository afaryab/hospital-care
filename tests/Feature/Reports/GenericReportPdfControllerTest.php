<?php

use App\Models\ExpenseVoucher;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('service order detail pdf renders successfully with a shared expense voucher', function () {
    $user = User::factory()->create();
    actingAs($user);

    $order = ServiceOrder::factory()->create();
    $otherOrder = ServiceOrder::factory()->create();

    $voucher = ExpenseVoucher::factory()->create(['amount' => 800]);
    $voucher->serviceOrders()->attach([$order->id, $otherOrder->id]);

    get(route('reports.generic.service-order', ['id' => $order->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});

test('service orders list pdf renders successfully with a shared expense voucher', function () {
    $user = User::factory()->create();
    actingAs($user);

    $order = ServiceOrder::factory()->create();
    $otherOrder = ServiceOrder::factory()->create();

    $voucher = ExpenseVoucher::factory()->create([
        'amount' => 800,
        'transaction_id' => Transaction::factory(),
        'transaction_element_id' => TransactionElement::factory(),
    ]);
    $voucher->serviceOrders()->attach([$order->id, $otherOrder->id]);

    get(route('reports.generic.service-orders'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});
