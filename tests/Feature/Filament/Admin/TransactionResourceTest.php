<?php

use App\Filament\Admin\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Admin\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Admin\Resources\Transactions\Pages\ViewTransaction;
use App\Models\Administrator;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;

afterEach(function () {
    \Carbon\Carbon::setTestNow();
});

beforeEach(function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);
    actingAs($user);
});

test('admin can list transactions with search and filters', function () {
    $matching = Transaction::factory()->create(['tr_number' => 'TR/2026/03/29/9001']);
    $nonMatching = Transaction::factory()->create(['tr_number' => 'TR/2026/03/29/9002']);

    Livewire\Livewire::test(ListTransactions::class)
        ->assertSuccessful()
        ->searchTable('9001')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$nonMatching]);
});

test('admin can view transaction details with elements', function () {
    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/29/1111']);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'type' => 'OPD',
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    Livewire\Livewire::test(ViewTransaction::class, ['record' => $transaction->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('TR/2026/03/29/1111')
        ->assertSee('OPD')
        ->assertSee('500.00');
});

test('admin can filter transactions by income expense', function () {
    $incomeTransaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'tr_number' => 'TR/2026/03/29/2101',
    ]);

    $expenseTransaction = Transaction::factory()->create([
        'income_or_expense' => 'EXPENSE',
        'tr_number' => 'TR/2026/03/29/2102',
    ]);

    Livewire\Livewire::test(ListTransactions::class)
        ->assertSuccessful()
        ->filterTable('income_or_expense', 'INCOME')
        ->assertCanSeeTableRecords([$incomeTransaction])
        ->assertCanNotSeeTableRecords([$expenseTransaction]);
});

test('admin can filter transactions by date range', function () {
    $insideRange = Transaction::factory()->create([
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $outsideRange = Transaction::factory()->create([
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ]);

    Livewire\Livewire::test(ListTransactions::class)
        ->assertSuccessful()
        ->filterTable('date_range', [
            'from' => now()->subDays(2)->toDateString(),
            'until' => now()->toDateString(),
        ])
        ->assertCanSeeTableRecords([$insideRange])
        ->assertCanNotSeeTableRecords([$outsideRange]);
});

test('admin can refund transaction from edit page', function () {
    $transaction = Transaction::factory()->create([
        'is_refunded' => false,
        'amount' => 750,
        'orignal_amount' => 750,
    ]);

    $receaveable = Receaveable::factory()->create([
        'transaction_id' => $transaction->id,
        'patient_id' => $transaction->patient_id,
        'status' => 'unpaid',
        'amount' => 750,
        'orignal_amount' => 750,
    ]);

    Livewire\Livewire::test(EditTransaction::class, ['record' => $transaction->getRouteKey()])
        ->assertSuccessful()
        ->callAction('refundTransaction');

    expect($transaction->fresh()->is_refunded)->toBeTrue()
        ->and($receaveable->fresh()->status)->toBe('cancelled');

    $refundElement = TransactionElement::query()->where('refunded_transaction_id', $transaction->id)->first();
    expect($refundElement)->not->toBeNull();
});
