<?php

use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    expect($trNumber)->toStartWith('TR/'.$now->format('Y').'/'.$now->format('m').'/'.$now->format('d').'/');
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

test('transaction generateTransactionNumber uses highest sequence and ignores gaps', function () {
    $now = now();
    $prefix = sprintf('TR/%s/%s/%s/', $now->format('Y'), $now->format('m'), $now->format('d'));

    Transaction::factory()->create(['tr_number' => $prefix.'0001']);
    Transaction::factory()->create(['tr_number' => $prefix.'0050']);

    expect(Transaction::generateTransactionNumber())->toBe($prefix.'0051');
});

test('transaction belongs to patient relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->patient())->toBeInstanceOf(BelongsTo::class);
});

test('transaction belongs to closing relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->closing())->toBeInstanceOf(BelongsTo::class);
});

test('transaction has many elements relationship', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->elements())->toBeInstanceOf(HasMany::class);
});

test('recalculatePayment creates a receivable when the customer pays less than the total billed', function () {
    $transaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'customer_payed' => 300,
    ]);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    $transaction->recalculatePayment();
    $transaction->refresh();

    expect((float) $transaction->amount)->toBe(300.0)
        ->and((float) $transaction->change)->toBe(0.0)
        ->and((float) $transaction->orignal_amount)->toBe(500.0);

    $receivable = Receaveable::where('transaction_id', $transaction->id)->first();

    expect($receivable)->not->toBeNull()
        ->and((float) $receivable->amount)->toBe(200.0)
        ->and((float) $receivable->orignal_amount)->toBe(200.0)
        ->and($receivable->status)->toBe('unpaid');
});

test('recalculatePayment computes change when the customer overpays and leaves no receivable', function () {
    $transaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'customer_payed' => 600,
    ]);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    $transaction->recalculatePayment();
    $transaction->refresh();

    expect((float) $transaction->amount)->toBe(500.0)
        ->and((float) $transaction->change)->toBe(100.0);

    expect(Receaveable::where('transaction_id', $transaction->id)->exists())->toBeFalse();
});

test('recalculatePayment preserves already-collected receivable payments when the total billed changes', function () {
    $transaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'customer_payed' => 300,
    ]);

    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    $transaction->recalculatePayment();

    // Shortfall is 200; simulate 150 already collected against it via the
    // Receivables workflow, leaving 50 outstanding.
    $receivable = Receaveable::where('transaction_id', $transaction->id)->first();
    $receivable->update(['amount' => 50]);

    // Admin corrects the line item charge upward.
    $element->update(['amount' => 700]);
    $transaction->recalculatePayment();
    $receivable->refresh();

    // New total billed 700, customer_payed 300 => new shortfall 400, minus
    // the 150 already collected leaves 250 outstanding.
    expect((float) $receivable->orignal_amount)->toBe(400.0)
        ->and((float) $receivable->amount)->toBe(250.0)
        ->and($receivable->status)->toBe('unpaid');
});

test('recalculatePayment marks the receivable paid once customer_payed covers the new total', function () {
    $transaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'customer_payed' => 300,
    ]);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    $transaction->recalculatePayment();

    $transaction->update(['customer_payed' => 500]);
    $transaction->recalculatePayment();
    $transaction->refresh();

    $receivable = Receaveable::where('transaction_id', $transaction->id)->first();

    expect((float) $transaction->amount)->toBe(500.0)
        ->and((float) $transaction->change)->toBe(0.0)
        ->and((float) $receivable->amount)->toBe(0.0)
        ->and($receivable->status)->toBe('paid');
});

test('recalculatePayment does not touch a transaction that is itself a receivable payment', function () {
    $receivable = Receaveable::factory()->create();
    $payment = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
        'amount' => 200,
    ]);

    $payment->recalculatePayment();

    expect((float) $payment->fresh()->amount)->toBe(200.0);
});

test('applyCollectedAmountDelta reduces the settled receivable balance when more is collected', function () {
    $receivable = Receaveable::factory()->create(['amount' => 500]);
    $payment = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
        'amount' => 200,
    ]);

    // Amount collected edited from 200 up to 350 — a delta of +150.
    $payment->applyCollectedAmountDelta(150);

    expect((float) $receivable->fresh()->amount)->toBe(350.0);
});

test('applyCollectedAmountDelta marks the settled receivable paid once fully collected', function () {
    $receivable = Receaveable::factory()->create(['amount' => 100]);
    $payment = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
        'amount' => 100,
    ]);

    $payment->applyCollectedAmountDelta(100);

    $fresh = $receivable->fresh();
    expect((float) $fresh->amount)->toBe(0.0)
        ->and($fresh->status)->toBe('paid');
});

test('applyCollectedAmountDelta restores balance when the collected amount is corrected downward', function () {
    $receivable = Receaveable::factory()->create(['amount' => 0, 'status' => 'paid']);
    $payment = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
        'amount' => 300,
    ]);

    // Admin realizes only 200 was actually collected, correcting amount
    // downward by 100 — the receivable should reopen for the difference.
    $payment->applyCollectedAmountDelta(-100);

    $fresh = $receivable->fresh();
    expect((float) $fresh->amount)->toBe(100.0)
        ->and($fresh->status)->toBe('unpaid');
});
