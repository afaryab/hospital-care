<?php

use App\Models\Receaveable;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;

test('observer creates a service order when a normal income element with service_id is created', function () {
    $service = Service::factory()->create();
    $transaction = Transaction::factory()->create(['receaveable_id' => null]);

    $before = ServiceOrder::count();

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_id' => $service->id,
        'type' => 'OPD',
        'income_or_expense' => 'INCOME',
    ]);

    expect(ServiceOrder::count())->toBe($before + 1);
});

test('observer does NOT create a service order for a receivable-payment element', function () {
    $service = Service::factory()->create();
    $receivable = Receaveable::factory()->create();
    // Payment transaction is tied to an existing receivable.
    $paymentTransaction = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
    ]);

    $before = ServiceOrder::count();

    // Even if the element happens to carry a service_id, no new SO should be created.
    TransactionElement::factory()->create([
        'transaction_id' => $paymentTransaction->id,
        'service_id' => $service->id,
        'type' => 'OPD',
        'income_or_expense' => 'INCOME',
    ]);

    expect(ServiceOrder::count())->toBe($before);
});

test('observer skips service order creation when service_id is missing', function () {
    $transaction = Transaction::factory()->create();
    $before = ServiceOrder::count();

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_id' => null,
        'type' => 'EXP',
        'income_or_expense' => 'EXPENSE',
    ]);

    expect(ServiceOrder::count())->toBe($before);
});
