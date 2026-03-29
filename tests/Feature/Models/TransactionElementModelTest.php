<?php

use App\Models\TransactionElement;

test('transaction element belongs to transaction', function () {
    $element = TransactionElement::factory()->create();

    expect($element->transaction())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($element->transaction)->not->toBeNull();
});

test('transaction element belongs to patient', function () {
    $element = TransactionElement::factory()->create();

    expect($element->patient())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('transaction element belongs to service', function () {
    $element = TransactionElement::factory()->create();

    expect($element->service())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('transaction element can be created with factory', function () {
    $element = TransactionElement::factory()->create();

    $this->assertDatabaseHas('transaction_elements', ['id' => $element->id]);
});
