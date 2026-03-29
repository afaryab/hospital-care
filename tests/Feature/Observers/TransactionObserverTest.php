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

test('transaction tr_number is immutable after creation', function () {
    $transaction = Transaction::factory()->create(['tr_number' => null]);
    $originalNumber = $transaction->tr_number;

    $transaction->tr_number = 'TR/9999/99/99/9999';
    $transaction->save();
    $transaction->refresh();

    expect($transaction->tr_number)->toBe($originalNumber);
});

test('transaction tr_number format matches expected pattern', function () {
    $transaction = Transaction::factory()->create(['tr_number' => null]);

    expect($transaction->tr_number)->toMatch('/^TR\/\d{4}\/\d{2}\/\d{2}\/\d{4}$/');
});

test('transaction observer does not overwrite an already set tr_number on create', function () {
    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2024/01/01/0001']);

    expect($transaction->tr_number)->toBe('TR/2024/01/01/0001');
});

test('transaction edited_amount is not set when amount is unchanged', function () {
    $transaction = Transaction::factory()->create(['amount' => 500, 'edited_amount' => null]);

    $transaction->notes = 'Updated notes';
    $transaction->save();
    $transaction->refresh();

    expect($transaction->edited_amount)->toBeNull();
});

