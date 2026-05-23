<?php

use App\Models\ExpenseVoucher;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Services\ServiceOrderMerger;
use Spatie\Activitylog\Models\Activity;

test('merger repoints transaction elements and soft-deletes duplicates', function () {
    $primary = ServiceOrder::factory()->create();
    $duplicate = ServiceOrder::factory()->create([
        'patient_id' => $primary->patient_id,
    ]);

    $transaction = Transaction::factory()->create(['receaveable_id' => null]);

    // Element on the duplicate that should be repointed.
    $element = TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $duplicate->id,
        'service_id' => null, // avoid observer spawning another SO
        'type' => 'OPD',
        'income_or_expense' => 'INCOME',
    ]);

    $result = app(ServiceOrderMerger::class)
        ->merge($primary, collect([$primary, $duplicate]), 'duplicate cleanup');

    expect($result['merged_ids'])->toBe([$duplicate->id])
        ->and($result['counts']['transaction_elements'])->toBe(1);

    expect($element->fresh()->service_order_id)->toBe($primary->id);

    // Duplicate is soft-deleted; primary remains.
    expect(ServiceOrder::find($duplicate->id))->toBeNull()
        ->and(ServiceOrder::withTrashed()->find($duplicate->id))->not->toBeNull()
        ->and(ServiceOrder::find($primary->id))->not->toBeNull();
});

test('merger moves expense vouchers attached to duplicates', function () {
    $primary = ServiceOrder::factory()->create();
    $duplicate = ServiceOrder::factory()->create([
        'patient_id' => $primary->patient_id,
    ]);

    $voucher = ExpenseVoucher::factory()->create(['service_order_id' => $duplicate->id]);

    app(ServiceOrderMerger::class)
        ->merge($primary, collect([$primary, $duplicate]), 'merge test');

    expect($voucher->fresh()->service_order_id)->toBe($primary->id);
});

test('merger logs an activity record on the primary', function () {
    $primary = ServiceOrder::factory()->create();
    $duplicate = ServiceOrder::factory()->create([
        'patient_id' => $primary->patient_id,
    ]);

    app(ServiceOrderMerger::class)
        ->merge($primary, collect([$primary, $duplicate]), 'why');

    $activity = Activity::query()
        ->where('description', 'service_orders_merged')
        ->where('subject_id', $primary->id)
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->properties['merged_ids'])->toBe([$duplicate->id])
        ->and($activity->properties['reason'])->toBe('why');
});

test('merger is a no-op when only the primary is in the collection', function () {
    $primary = ServiceOrder::factory()->create();
    $before = ServiceOrder::count();

    $result = app(ServiceOrderMerger::class)
        ->merge($primary, collect([$primary]), 'nothing to do');

    expect($result['merged_ids'])->toBe([])
        ->and(ServiceOrder::count())->toBe($before);
});
