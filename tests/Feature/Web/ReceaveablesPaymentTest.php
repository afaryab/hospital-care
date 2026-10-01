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

/**
 * @return array{0: User, 1: Closing, 2: Receaveable}
 */
function multiServiceReceivable(float $outstanding = 22000, string $status = 'unpaid'): array
{
    $user = User::factory()->create();
    $closing = Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    $patient = Patient::factory()->create();

    $originalTransaction = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'patient_id' => $patient->id,
        'income_or_expense' => 'INCOME',
        'amount' => 8000,
    ]);

    foreach ([12000, 18000] as $amount) {
        TransactionElement::factory()->create([
            'closing_id' => $closing->id,
            'transaction_id' => $originalTransaction->id,
            'patient_id' => $patient->id,
            'income_or_expense' => 'INCOME',
            'amount' => $amount,
        ]);
    }

    $receaveable = Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'transaction_id' => $originalTransaction->id,
        'amount' => $outstanding,
        'orignal_amount' => $outstanding,
        'status' => $status,
    ]);

    return [$user, $closing, $receaveable];
}

test('a receivable from a bill with several services can be collected', function () {
    [$user, $closing, $receaveable] = multiServiceReceivable();

    actingAs($user);

    post(route('receaveables-payment'), [
        'patient_id' => $receaveable->patient_id,
        'receaveable_id' => $receaveable->id,
        'amount_to_collect' => 22000,
        'payment_method' => 'BANK_TRANSFER',
        'receaveable_note' => '',
    ])->assertSessionHasNoErrors()->assertRedirectContains('/TR/');

    $settlement = Transaction::where('receaveable_id', $receaveable->id)->sole();
    expect((float) $settlement->amount)->toBe(22000.0)
        ->and($settlement->type)->toBe('BANK_TRANSFER')
        ->and($settlement->closing_id)->toBe($closing->id);

    $receaveable->refresh();
    expect($receaveable->status)->toBe('paid')
        ->and((float) $receaveable->amount)->toBe(0.0);
});

test('a partial collection keeps the rest outstanding and stores the note', function () {
    [$user, , $receaveable] = multiServiceReceivable();

    actingAs($user);

    post(route('receaveables-payment'), [
        'receaveable_id' => $receaveable->id,
        'amount_to_collect' => 5000,
        'payment_method' => 'CASH',
        'receaveable_note' => 'Paid by brother, balance next visit',
    ])->assertSessionHasNoErrors();

    expect(Transaction::where('receaveable_id', $receaveable->id)->sole()->notes)->toBe('Paid by brother, balance next visit');

    $receaveable->refresh();
    expect($receaveable->status)->toBe('unpaid')
        ->and((float) $receaveable->amount)->toBe(17000.0);
});

test('a rejected collection reports the reason against the amount field', function (float $amount, string $status, string $message) {
    [$user, , $receaveable] = multiServiceReceivable(status: $status);

    actingAs($user);

    post(route('receaveables-payment'), [
        'receaveable_id' => $receaveable->id,
        'amount_to_collect' => $amount,
        'payment_method' => 'CASH',
    ])->assertSessionHasErrors('amount_to_collect');

    expect(session('errors')->first('amount_to_collect'))->toContain($message);

    expect(Transaction::where('receaveable_id', $receaveable->id)->exists())->toBeFalse();
})->with([
    'more than outstanding' => [25000, 'unpaid', 'only has 22,000.00 outstanding'],
    'already paid' => [1000, 'paid', 'is paid and cannot take a payment'],
]);

test('collecting without an open counter explains why instead of silently redirecting', function () {
    [$user, $closing, $receaveable] = multiServiceReceivable();
    $closing->update(['status' => 'closed']);

    actingAs($user);

    post(route('receaveables-payment'), [
        'receaveable_id' => $receaveable->id,
        'amount_to_collect' => 1000,
        'payment_method' => 'CASH',
    ])->assertRedirect(route('counter-open'));

    expect((float) $receaveable->fresh()->amount)->toBe(22000.0);
});
