<?php

use App\Models\Transaction;

test('transaction observer auto-generates tr_number on create', function () {
    $transaction = Transaction::factory()->create(['tr_number' => null]);

    expect($transaction->tr_number)->not->toBeNull()
        ->and($transaction->tr_number)->toStartWith('TR/');
});

test('transaction observer preserves provided tr_number', function () {
    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/29/9999']);

    expect($transaction->tr_number)->toBe('TR/2026/03/29/9999');
});

test('transaction observer stores edited_amount when amount changes', function () {
    $transaction = Transaction::factory()->create(['amount' => 1000]);
    $originalAmount = $transaction->amount;

    $transaction->amount = 2000;
    $transaction->save();
    $transaction->refresh();

    expect($transaction->edited_amount)->toBe($originalAmount);
});
