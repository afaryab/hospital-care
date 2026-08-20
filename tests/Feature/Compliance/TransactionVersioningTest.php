<?php

use App\Models\Transaction;
use App\Models\TransactionVersion;
use App\Models\User;

test('updating a transaction creates a version snapshot of the pre-change data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transaction = Transaction::factory()->create(['amount' => 500]);

    $transaction->update(['notes' => 'Corrected note']);

    $version = TransactionVersion::where('transaction_id', $transaction->id)->latest('id')->first();

    expect($version)->not->toBeNull()
        ->and((float) $version->snapshot['amount'])->toBe(500.0)
        ->and($version->changed_by)->toBe($user->id)
        ->and($version->change_reason)->toBe('record_update');
});

test('multiple updates create multiple ordered version snapshots', function () {
    $transaction = Transaction::factory()->create(['notes' => 'Original']);

    $transaction->update(['notes' => 'First edit']);
    $transaction->update(['notes' => 'Second edit']);

    $versions = TransactionVersion::where('transaction_id', $transaction->id)->orderBy('id')->pluck('snapshot');

    expect($versions)->toHaveCount(2)
        ->and($versions[0]['notes'])->toBe('Original')
        ->and($versions[1]['notes'])->toBe('First edit');
});

test('quiet recalculation writes do not create version snapshots', function () {
    $transaction = Transaction::factory()->create(['amount' => 100]);

    $transaction->updateQuietly(['amount' => 150]);

    expect(TransactionVersion::where('transaction_id', $transaction->id)->count())->toBe(0);
});

test('deleting a transaction soft-deletes it rather than removing the row', function () {
    $transaction = Transaction::factory()->create();

    $transaction->delete();

    expect(Transaction::find($transaction->id))->toBeNull()
        ->and(Transaction::withTrashed()->find($transaction->id))->not->toBeNull()
        ->and(Transaction::withTrashed()->find($transaction->id)->deleted_at)->not->toBeNull();
});

test('force deleting a transaction is blocked', function () {
    $transaction = Transaction::factory()->create();

    expect(fn () => $transaction->forceDelete())->toThrow(RuntimeException::class);
});
