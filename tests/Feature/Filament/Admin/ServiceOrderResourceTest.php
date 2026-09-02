<?php

use App\Filament\Admin\Resources\ServiceOrders\Pages\ListServiceOrders;
use App\Filament\Admin\Resources\ServiceOrders\Pages\ViewServiceOrder;
use App\Models\Administrator;
use App\Models\ExpenseVoucher;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('service order list page renders', function () {
    Livewire\Livewire::test(ListServiceOrders::class)->assertSuccessful();
});

test('service order list vouchers amount column divides a shared expense voucher', function () {
    $serviceOrder = ServiceOrder::factory()->create();
    $otherOrder = ServiceOrder::factory()->create();

    $voucher = ExpenseVoucher::factory()->create(['amount' => 900]);
    $voucher->serviceOrders()->attach([$serviceOrder->id, $otherOrder->id]);

    Livewire\Livewire::test(ListServiceOrders::class)
        ->call('loadTable')
        ->assertTableColumnStateSet('expense_vouchers_sum_amount', 450.0, record: $serviceOrder);
});

test('service order view page renders', function () {
    $serviceOrder = ServiceOrder::factory()->create();
    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])->assertSuccessful();
});

test('service order view page shows the divided share of a shared expense voucher', function () {
    $serviceOrder = ServiceOrder::factory()->create();
    $otherOrder = ServiceOrder::factory()->create();

    $voucher = ExpenseVoucher::factory()->create(['amount' => 800]);
    $voucher->serviceOrders()->attach([$serviceOrder->id, $otherOrder->id]);

    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('400.00')
        ->assertSee('shared across 2 orders');
});

test('service order list can be filtered by triage', function () {
    $triage = Triage::factory()->create();

    $matching = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0001/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $matching->id, 'triage_id' => $triage->id]);

    $other = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0002/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $other->id]);

    Livewire\Livewire::test(ListServiceOrders::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$matching, $other])
        ->filterTable('triage', $triage->id)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

test('visiting the service order list with a triage query param pre-applies the filter', function () {
    $triage = Triage::factory()->create();

    $matching = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0003/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $matching->id, 'triage_id' => $triage->id]);

    $other = ServiceOrder::factory()->create(['so_number' => 'PS/2026/01/0004/EMG/01']);
    TreatmentRecord::factory()->create(['service_order_id' => $other->id]);

    Livewire\Livewire::withQueryParams(['triage' => (string) $triage->id])
        ->test(ListServiceOrders::class)
        ->assertSuccessful()
        ->call('loadTable')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
});

test('edit charges action recalculates the transaction and receivable from the service order page', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    $transaction = Transaction::factory()->create([
        'patient_id' => $serviceOrder->patient_id,
        'income_or_expense' => 'INCOME',
        'customer_payed' => 500,
    ]);

    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $serviceOrder->patient_id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])
        ->assertSuccessful()
        ->callAction('edit_charges', data: [
            "txn_{$transaction->id}_line_{$element->id}" => 500,
            "txn_{$transaction->id}_customer_payed" => 300,
            'reason' => 'Patient given a discount after the fact.',
        ])
        ->assertHasNoFormErrors();

    $freshTransaction = $transaction->fresh();

    expect((float) $freshTransaction->customer_payed)->toBe(300.0)
        ->and((float) $freshTransaction->amount)->toBe(300.0)
        ->and((float) $freshTransaction->change)->toBe(0.0);

    $receivable = Receaveable::where('transaction_id', $transaction->id)->first();
    expect($receivable)->not->toBeNull()
        ->and((float) $receivable->amount)->toBe(200.0)
        ->and($receivable->status)->toBe('unpaid');
});

test('edit charges action honors a manual receivable override over the automatic recalculation', function () {
    $serviceOrder = ServiceOrder::factory()->create();

    $transaction = Transaction::factory()->create([
        'patient_id' => $serviceOrder->patient_id,
        'income_or_expense' => 'INCOME',
        'customer_payed' => 300,
    ]);

    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'closing_id' => $transaction->closing_id,
        'patient_id' => $serviceOrder->patient_id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    $receivable = Receaveable::factory()->create([
        'transaction_id' => $transaction->id,
        'patient_id' => $serviceOrder->patient_id,
        'amount' => 200,
        'orignal_amount' => 200,
        'status' => 'unpaid',
    ]);

    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])
        ->assertSuccessful()
        ->callAction('edit_charges', data: [
            "txn_{$transaction->id}_line_{$element->id}" => 500,
            "txn_{$transaction->id}_customer_payed" => 300,
            "txn_{$transaction->id}_receivable_amount" => 0,
            "txn_{$transaction->id}_receivable_status" => 'cancelled',
            'reason' => 'Waiving the outstanding balance.',
        ])
        ->assertHasNoFormErrors();

    $freshReceivable = $receivable->fresh();
    expect((float) $freshReceivable->amount)->toBe(0.0)
        ->and($freshReceivable->status)->toBe('cancelled');
});
