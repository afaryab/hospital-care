<?php

use App\Filament\Admin\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Admin\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Admin\Resources\Transactions\Pages\ViewTransaction;
use App\Models\Administrator;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;

afterEach(function () {
    Carbon::setTestNow();
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
        ->assertSee("PKR\u{A0}500");
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

test('editing customer_payed on the edit page recalculates recognized amount, change, and receivable', function () {
    $transaction = Transaction::factory()->create([
        'income_or_expense' => 'INCOME',
        'customer_payed' => 500,
        'amount' => 500,
    ]);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $transaction->patient_id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    Livewire\Livewire::test(EditTransaction::class, ['record' => $transaction->getRouteKey()])
        ->assertSuccessful()
        ->fillForm(['customer_payed' => 300])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $transaction->fresh();

    expect((float) $fresh->customer_payed)->toBe(300.0)
        ->and((float) $fresh->amount)->toBe(300.0)
        ->and((float) $fresh->change)->toBe(0.0);

    $receivable = Receaveable::where('transaction_id', $transaction->id)->first();
    expect($receivable)->not->toBeNull()
        ->and((float) $receivable->amount)->toBe(200.0)
        ->and($receivable->status)->toBe('unpaid');
});

test('editing amount on a receivable-payment transaction adjusts the settled receivable balance', function () {
    $receivable = Receaveable::factory()->create([
        'amount' => 500,
        'orignal_amount' => 800,
        'status' => 'unpaid',
    ]);

    $payment = Transaction::factory()->create([
        'receaveable_id' => $receivable->id,
        'patient_id' => $receivable->patient_id,
        'amount' => 200,
    ]);

    Livewire\Livewire::test(EditTransaction::class, ['record' => $payment->getRouteKey()])
        ->assertSuccessful()
        ->fillForm(['amount' => 350])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) $payment->fresh()->amount)->toBe(350.0)
        ->and((float) $receivable->fresh()->amount)->toBe(350.0);
});
