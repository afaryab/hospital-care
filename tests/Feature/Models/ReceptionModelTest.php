<?php

use App\Models\Closing;
use App\Models\Reception;

test('reception can be created with factory', function () {
    $reception = Reception::factory()->create();

    expect($reception)->toBeInstanceOf(Reception::class)
        ->and($reception->name)->not->toBeNull()
        ->and($reception->is_cash_allowed)->toBeTrue()
        ->and($reception->is_allowed_to_pay_voucher)->toBeFalse();
});

test('reception boolean fields are cast correctly', function () {
    $reception = Reception::factory()->create([
        'is_allowed_to_pay_voucher' => true,
        'is_allowed_to_pay_from_petty_cash' => true,
        'is_cash_allowed' => false,
        'is_cheques_allowed' => true,
        'is_card_allowed' => true,
    ]);

    expect($reception->is_allowed_to_pay_voucher)->toBeBool()->toBeTrue()
        ->and($reception->is_allowed_to_pay_from_petty_cash)->toBeBool()->toBeTrue()
        ->and($reception->is_cash_allowed)->toBeBool()->toBeFalse()
        ->and($reception->is_cheques_allowed)->toBeBool()->toBeTrue()
        ->and($reception->is_card_allowed)->toBeBool()->toBeTrue();
});

test('reception allowed_departments is cast to json', function () {
    $departments = ['OPD', 'LAB'];
    $reception = Reception::factory()->create(['allowed_departments' => $departments]);

    expect($reception->allowed_departments)->toBe($departments);
});

test('reception has many closings', function () {
    $reception = Reception::factory()->create();
    $closing = Closing::factory()->create(['reception_id' => $reception->id]);

    expect($reception->closings()->count())->toBe(1)
        ->and($reception->closings->first()->id)->toBe($closing->id);
});

test('reception closings returns empty collection when none exist', function () {
    $reception = Reception::factory()->create();

    expect($reception->closings)->toBeEmpty();
});

test('reception fillable attributes can be mass assigned', function () {
    $reception = Reception::create([
        'name' => 'Main Reception',
        'is_allowed_to_pay_voucher' => true,
        'is_allowed_to_pay_from_petty_cash' => false,
        'is_cash_allowed' => true,
        'is_cheques_allowed' => false,
        'is_card_allowed' => false,
    ]);

    expect($reception->name)->toBe('Main Reception')
        ->and($reception->is_allowed_to_pay_voucher)->toBeTrue();
});
