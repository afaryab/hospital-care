<?php

use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('receaveable can be created with factory', function () {
    $receaveable = Receaveable::factory()->create();

    $this->assertDatabaseHas('receaveables', ['id' => $receaveable->id]);
});

test('receaveable belongs to patient', function () {
    $receaveable = Receaveable::factory()->create();

    expect($receaveable->patient())->toBeInstanceOf(BelongsTo::class)
        ->and($receaveable->patient)->not->toBeNull();
});

test('receaveable belongs to transaction', function () {
    $receaveable = Receaveable::factory()->create();

    expect($receaveable->transaction())->toBeInstanceOf(BelongsTo::class)
        ->and($receaveable->transaction)->not->toBeNull();
});

test('receaveable paid state sets amount to zero', function () {
    $receaveable = Receaveable::factory()->paid()->create();

    expect((float) $receaveable->amount)->toBe(0.0)
        ->and($receaveable->status)->toBe('PAID');
});

test('receaveable resolves its linked service order through the transaction element', function () {
    $transaction = Transaction::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create();
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
    ]);
    $receaveable = Receaveable::factory()->create(['transaction_id' => $transaction->id]);

    expect($receaveable->serviceOrder)->not->toBeNull()
        ->and($receaveable->serviceOrder->id)->toBe($serviceOrder->id);
});

test('receaveable has no linked service order when its transaction element has none', function () {
    $transaction = Transaction::factory()->create();
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => null,
        'income_or_expense' => 'INCOME',
    ]);
    $receaveable = Receaveable::factory()->create(['transaction_id' => $transaction->id]);

    expect($receaveable->serviceOrder)->toBeNull();
});

test('receaveable lists its payment transactions', function () {
    $receaveable = Receaveable::factory()->create();
    $payment = Transaction::factory()->create(['receaveable_id' => $receaveable->id]);
    Transaction::factory()->create(); // unrelated transaction

    expect($receaveable->payments)->toHaveCount(1)
        ->and($receaveable->payments->first()->id)->toBe($payment->id);
});
