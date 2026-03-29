<?php

use App\Models\Receaveable;

test('receaveable can be created with factory', function () {
    $receaveable = Receaveable::factory()->create();

    $this->assertDatabaseHas('receaveables', ['id' => $receaveable->id]);
});

test('receaveable belongs to patient', function () {
    $receaveable = Receaveable::factory()->create();

    expect($receaveable->patient())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($receaveable->patient)->not->toBeNull();
});

test('receaveable belongs to transaction', function () {
    $receaveable = Receaveable::factory()->create();

    expect($receaveable->transaction())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($receaveable->transaction)->not->toBeNull();
});

test('receaveable paid state sets amount to zero', function () {
    $receaveable = Receaveable::factory()->paid()->create();

    expect((float) $receaveable->amount)->toBe(0.0)
        ->and($receaveable->status)->toBe('PAID');
});
