<?php

use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('receivables page redirects to open counter when none is open', function () {
    actingAs(User::factory()->create());

    get(route('receaveables'))->assertRedirect(route('counter-open'));
});

test('receivables defaults to showing only unpaid receivables', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    $unpaid = Receaveable::factory()->create(['status' => 'unpaid']);
    Receaveable::factory()->create(['status' => 'paid']);

    get(route('receaveables'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('receaveables.data', 1)
            ->where('receaveables.data.0.id', $unpaid->id)
        );
});

test('receivables status filter can show paid or all', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    Receaveable::factory()->create(['status' => 'unpaid']);
    $paid = Receaveable::factory()->create(['status' => 'paid']);

    get(route('receaveables', ['status' => 'paid']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('receaveables.data', 1)
            ->where('receaveables.data.0.id', $paid->id)
        );

    get(route('receaveables', ['status' => 'all']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('receaveables.data', 2));
});

test('receivables search matches by patient name, ps_number, and service order numbers', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    $patient = Patient::factory()->create(['name' => 'Zara Bibi', 'ps_number' => 'PS/2026/01/0055']);
    $transaction = Transaction::factory()->create(['patient_id' => $patient->id]);
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id, 'so_number' => 'PS/2026/01/0055/OPD/01', 'so_short' => '77778888']);
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
    ]);
    $match = Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'transaction_id' => $transaction->id,
        'status' => 'unpaid',
    ]);

    Receaveable::factory()->create(['status' => 'unpaid']);

    foreach (['Zara', '0055', '77778888'] as $term) {
        get(route('receaveables', ['status' => 'all', 'search' => $term]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('receaveables.data', 1)
                ->where('receaveables.data.0.id', $match->id)
            );
    }
});

test('receivables row exposes its linked service order, payments, and divided expense vouchers', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    $patient = Patient::factory()->create();
    $transaction = Transaction::factory()->create(['patient_id' => $patient->id]);
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id]);
    $otherOrder = ServiceOrder::factory()->create();
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
    ]);
    $receaveable = Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'transaction_id' => $transaction->id,
        'status' => 'unpaid',
    ]);
    $payment = Transaction::factory()->create(['receaveable_id' => $receaveable->id]);

    $voucher = ExpenseVoucher::factory()->create(['amount' => 600]);
    $voucher->serviceOrders()->attach([$serviceOrder->id, $otherOrder->id]);

    get(route('receaveables', ['status' => 'all']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('receaveables.data.0.linked_service_order.id', $serviceOrder->id)
            ->where('receaveables.data.0.payments.0.id', $payment->id)
            ->where('receaveables.data.0.expense_vouchers.0.share_amount', 300)
        );
});

test('receivables row gracefully handles a receivable with no linked service order', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    Receaveable::factory()->create(['status' => 'unpaid']);

    get(route('receaveables'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('receaveables.data.0.linked_service_order', null)
            ->where('receaveables.data.0.expense_vouchers', [])
        );
});
