<?php

use App\Models\Closing;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

test('collecting a receivable in the same shift does not double count income or the service order', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->create([
        'status' => 'open',
        'receptionist_id' => $user->id,
    ]);
    $patient = Patient::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create(['patient_id' => $patient->id]);

    // Original sale: full service amount (1000) recognised on the element, but the
    // patient only paid 600 at the counter, leaving a 400 receivable.
    $originalTransaction = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'patient_id' => $patient->id,
        'income_or_expense' => 'INCOME',
        'amount' => 600,
    ]);

    TransactionElement::factory()->create([
        'closing_id' => $closing->id,
        'transaction_id' => $originalTransaction->id,
        'patient_id' => $patient->id,
        'service_order_id' => $serviceOrder->id,
        'service_id' => null,
        'income_or_expense' => 'INCOME',
        'amount' => 1000,
    ]);

    $receaveable = Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'transaction_id' => $originalTransaction->id,
        'amount' => 400,
        'orignal_amount' => 400,
        'status' => 'unpaid',
    ]);

    actingAs($user);

    post(route('receaveables-payment'), [
        'receaveable_id' => $receaveable->id,
        'payment_method' => 'CASH',
        'amount_to_collect' => 400,
    ])->assertRedirect();

    // The settlement is recorded as a cash Transaction (Dr Cash / Cr A/R) ...
    $settlement = Transaction::where('receaveable_id', $receaveable->id)->first();
    expect($settlement)->not->toBeNull();
    expect((float) $settlement->amount)->toBe(400.0);
    expect($settlement->income_or_expense)->toBe('INCOME');

    // ... but it must NOT create a second income element.
    expect($settlement->elements()->count())->toBe(0);

    // The service order's recognised income stays at the original service amount,
    // and only the original element remains linked to it.
    $serviceOrderIncome = TransactionElement::where('service_order_id', $serviceOrder->id)
        ->where('income_or_expense', 'INCOME')
        ->sum('amount');
    expect((float) $serviceOrderIncome)->toBe(1000.0);
    expect(TransactionElement::where('service_order_id', $serviceOrder->id)->count())->toBe(1);

    // The receivable is fully settled.
    $receaveable->refresh();
    expect($receaveable->status)->toBe('paid');
    expect((float) $receaveable->amount)->toBe(0.0);
});
