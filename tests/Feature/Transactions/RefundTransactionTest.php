<?php

use App\Models\Administrator;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('admin can mark a transaction as refunded', function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $transaction = Transaction::factory()->create(['is_refunded' => false]);

    postJson(route('api-transactions-refund', ['transaction' => $transaction->id]))
        ->assertOk();

    expect($transaction->fresh()->is_refunded)->toBeTrue();
});

test('refunding a transaction cancels related receivable', function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $transaction = Transaction::factory()->create(['is_refunded' => false]);

    $receaveable = Receaveable::factory()->create([
        'transaction_id' => $transaction->id,
        'patient_id' => $transaction->patient_id,
        'status' => 'unpaid',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    postJson(route('api-transactions-refund', ['transaction' => $transaction->id]))
        ->assertOk();

    expect($receaveable->fresh()->status)->toBe('cancelled')
        ->and((float) $receaveable->fresh()->amount)->toBe(0.0);
});

test('refund creates corresponding refund transaction element', function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);
    actingAs($admin);

    $transaction = Transaction::factory()->create([
        'is_refunded' => false,
        'amount' => 1200,
        'orignal_amount' => 1200,
    ]);

    postJson(route('api-transactions-refund', ['transaction' => $transaction->id]))
        ->assertOk();

    $refundElement = TransactionElement::query()
        ->where('refunded_transaction_id', $transaction->id)
        ->latest('id')
        ->first();

    expect($refundElement)->not->toBeNull()
        ->and($refundElement->income_or_expense)->toBe('EXPENSE')
        ->and((float) $refundElement->amount)->toBe(1200.0)
        ->and($refundElement->type)->toBe('REFUND');
});

test('non admin cannot refund a transaction', function () {
    $user = User::factory()->create();
    actingAs($user);

    $transaction = Transaction::factory()->create(['is_refunded' => false]);

    postJson(route('api-transactions-refund', ['transaction' => $transaction->id]))
        ->assertForbidden();

    expect($transaction->fresh()->is_refunded)->toBeFalse();
});
