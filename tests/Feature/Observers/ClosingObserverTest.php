<?php

use App\Models\Closing;
use App\Models\Reception;
use App\Models\User;
use App\Observers\ClosingObserver;

beforeEach(function () {
    // Register the observer for this test suite since it's not globally registered
    Closing::observe(ClosingObserver::class);
});

test('closing ct_number is auto-generated when not provided', function () {
    $reception = Reception::factory()->create();
    $user = User::factory()->create();

    $closing = Closing::create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'OPEN',
        'opening_amount' => 0,
    ]);

    expect($closing->ct_number)->not->toBeNull();
    expect($closing->ct_number)->toMatch('/^CT\/\d{4}\/\d{2}\/\d{4}$/');
});

test('closing ct_number format contains current year and month', function () {
    $reception = Reception::factory()->create();
    $user = User::factory()->create();

    $closing = Closing::create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'OPEN',
        'opening_amount' => 0,
    ]);

    $year = now()->format('Y');
    $month = now()->format('m');

    expect($closing->ct_number)->toContain("CT/{$year}/{$month}/");
});

test('closing ct_number is not overwritten when explicitly provided', function () {
    $closing = Closing::factory()->create(['ct_number' => 'CT/2023/01/9999']);

    expect($closing->ct_number)->toBe('CT/2023/01/9999');
});

test('closing ct_number cannot be changed after creation', function () {
    $closing = Closing::factory()->create();
    $original = $closing->ct_number;

    $closing->ct_number = 'CT/0000/00/0000';
    $closing->save();
    $closing->refresh();

    expect($closing->ct_number)->toBe($original);
});

test('closing ct_numbers are sequential when auto-generated', function () {
    $reception = Reception::factory()->create();
    $user = User::factory()->create();

    $first = Closing::create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'OPEN',
        'opening_amount' => 0,
    ]);

    $second = Closing::create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'OPEN',
        'opening_amount' => 0,
    ]);

    [$firstNum] = sscanf($first->ct_number, 'CT/%*d/%*d/%d');
    [$secondNum] = sscanf($second->ct_number, 'CT/%*d/%*d/%d');

    expect($secondNum)->toBe($firstNum + 1);
});
