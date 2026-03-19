<?php

use App\Models\Transaction;

test('transaction tr_number_parts parses correctly', function () {
    $transaction = Transaction::factory()->make([
        'tr_number' => 'TR/2026/03/19/0001',
    ]);

    expect($transaction->tr_number_parts)->toBe([
        'year' => '2026',
        'month' => '03',
        'day' => '19',
        'number' => '0001',
    ]);
});

test('transaction tr_number_parts returns null when tr_number is empty', function () {
    $transaction = Transaction::factory()->make(['tr_number' => null]);

    expect($transaction->tr_number_parts)->toBeNull();
});

test('transaction year month day number attributes derive from tr_number', function () {
    $transaction = Transaction::factory()->make([
        'tr_number' => 'TR/2026/03/19/0005',
    ]);

    expect($transaction->year)->toBe('2026')
        ->and($transaction->month)->toBe('03')
        ->and($transaction->day)->toBe('19')
        ->and($transaction->number)->toBe('0005');
});

test('transaction generateTransactionNumber returns correctly formatted tr number', function () {
    $trNumber = Transaction::generateTransactionNumber();
    $now = now();

    expect($trNumber)->toStartWith('TR/' . $now->format('Y') . '/' . $now->format('m') . '/' . $now->format('d') . '/');
    expect(explode('/', $trNumber))->toHaveCount(5);
    expect(strlen(explode('/', $trNumber)[4]))->toBe(4);
});

test('transaction generateTransactionNumber increments correctly', function () {
    $first = Transaction::generateTransactionNumber();
    Transaction::factory()->create(['tr_number' => $first]);
    $second = Transaction::generateTransactionNumber();

    $firstSeq = (int) explode('/', $first)[4];
    $secondSeq = (int) explode('/', $second)[4];

    expect($secondSeq)->toBe($firstSeq + 1);
});

test('transaction belongs to patient relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->patient())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('transaction belongs to closing relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->closing())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('transaction has many elements relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->elements())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});
