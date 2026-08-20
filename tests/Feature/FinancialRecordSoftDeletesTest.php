<?php

use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Receaveable;
use App\Models\Transaction;

test('deleting a transaction soft-deletes it rather than removing the row', function () {
    $transaction = Transaction::factory()->create();

    $transaction->delete();

    expect(Transaction::find($transaction->id))->toBeNull()
        ->and(Transaction::withTrashed()->find($transaction->id))->not->toBeNull()
        ->and(Transaction::withTrashed()->find($transaction->id)->deleted_at)->not->toBeNull();

    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
});

test('deleting a closing soft-deletes it rather than removing the row', function () {
    $closing = Closing::factory()->create();

    $closing->delete();

    expect(Closing::find($closing->id))->toBeNull()
        ->and(Closing::withTrashed()->find($closing->id))->not->toBeNull();

    $this->assertDatabaseHas('closings', ['id' => $closing->id]);
});

test('deleting an expense voucher soft-deletes it rather than removing the row', function () {
    $voucher = ExpenseVoucher::factory()->create();

    $voucher->delete();

    expect(ExpenseVoucher::find($voucher->id))->toBeNull()
        ->and(ExpenseVoucher::withTrashed()->find($voucher->id))->not->toBeNull();

    $this->assertDatabaseHas('expense_vouchers', ['id' => $voucher->id]);
});

test('deleting a receaveable soft-deletes it rather than removing the row', function () {
    $receaveable = Receaveable::factory()->create();

    $receaveable->delete();

    expect(Receaveable::find($receaveable->id))->toBeNull()
        ->and(Receaveable::withTrashed()->find($receaveable->id))->not->toBeNull();

    $this->assertDatabaseHas('receaveables', ['id' => $receaveable->id]);
});

test('a soft-deleted transaction can be restored', function () {
    $transaction = Transaction::factory()->create();
    $transaction->delete();

    $transaction->restore();

    expect(Transaction::find($transaction->id))->not->toBeNull();
});
