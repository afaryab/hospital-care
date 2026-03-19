<?php

use App\Models\Closing;

test('closing ct_number_parts parses correctly', function () {
    $closing = Closing::factory()->make(['ct_number' => 'CT/2026/03/0001']);

    expect($closing->ct_number_parts)->toBe([
        'year' => '2026',
        'month' => '03',
        'number' => '0001',
    ]);
});

test('closing ct_number_parts returns null when ct_number is empty', function () {
    $closing = Closing::factory()->make(['ct_number' => null]);

    expect($closing->ct_number_parts)->toBeNull();
});

test('closing year month number attributes derive from ct_number', function () {
    $closing = Closing::factory()->make(['ct_number' => 'CT/2026/03/0007']);

    expect($closing->year)->toBe('2026')
        ->and($closing->month)->toBe('03')
        ->and($closing->number)->toBe('0007');
});

test('closing generateCounterNumber returns correctly formatted ct number', function () {
    $ctNumber = Closing::generateCounterNumber();
    $now = now();

    expect($ctNumber)->toStartWith('CT/' . $now->format('Y') . '/' . $now->format('m') . '/');
    expect(explode('/', $ctNumber))->toHaveCount(4);
    expect(strlen(explode('/', $ctNumber)[3]))->toBe(4);
});

test('closing has many transactions relationship', function () {
    $closing = Closing::factory()->create();

    expect($closing->transactions())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('closing belongs to reception relationship', function () {
    $closing = Closing::factory()->create();

    expect($closing->reception())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('closing belongs to receptionist relationship', function () {
    $closing = Closing::factory()->create();

    expect($closing->receptionist())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('closing can be created with open status', function () {
    $closing = Closing::factory()->create(['status' => 'OPEN']);

    $this->assertDatabaseHas('closings', ['id' => $closing->id, 'status' => 'OPEN']);
});

test('closing can be created with closed status', function () {
    $closing = Closing::factory()->closed()->create();

    $this->assertDatabaseHas('closings', ['id' => $closing->id, 'status' => 'CLOSED']);
});
