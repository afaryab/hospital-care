<?php

use App\Models\TransactionElement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('transaction element belongs to transaction', function () {
    $element = TransactionElement::factory()->create();

    expect($element->transaction())->toBeInstanceOf(BelongsTo::class)
        ->and($element->transaction)->not->toBeNull();
});

test('transaction element belongs to patient', function () {
    $element = TransactionElement::factory()->create();

    expect($element->patient())->toBeInstanceOf(BelongsTo::class);
});

test('transaction element belongs to service', function () {
    $element = TransactionElement::factory()->create();

    expect($element->service())->toBeInstanceOf(BelongsTo::class);
});

test('transaction element can be created with factory', function () {
    $element = TransactionElement::factory()->create();

    $this->assertDatabaseHas('transaction_elements', ['id' => $element->id]);
});
