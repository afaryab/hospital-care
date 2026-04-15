<?php

use App\Models\Closing;
use App\Models\Reception;
use App\Models\Receptionist;
use App\Models\User;

test('counter open page shows only user bound receptions', function () {
    $user = User::factory()->create();

    $receptionA = Reception::factory()->create(['name' => 'Reception A']);
    $receptionB = Reception::factory()->create(['name' => 'Reception B']);
    $receptionC = Reception::factory()->create(['name' => 'Reception C']);

    Receptionist::factory()->create(['user_id' => $user->id, 'reception_id' => $receptionA->id]);
    Receptionist::factory()->create(['user_id' => $user->id, 'reception_id' => $receptionB->id]);

    $this->actingAs($user);

    $response = $this->get(route('counter-open'));

    $response->assertOk();

    $receptions = $response->original->getData()['page']['props']['recptions'];

    expect($receptions)->toHaveCount(2);

    $ids = collect($receptions)->pluck('id')->toArray();
    expect($ids)->toContain($receptionA->id, $receptionB->id);
    expect($ids)->not->toContain($receptionC->id);
});

test('counter open page shows all receptions when user has no bound receptions', function () {
    $user = User::factory()->create();

    Reception::factory()->count(3)->create();

    // Create receptionist profile without reception_id
    Receptionist::factory()->create(['user_id' => $user->id, 'reception_id' => null]);

    $this->actingAs($user);

    $response = $this->get(route('counter-open'));

    $response->assertOk();

    $receptions = $response->original->getData()['page']['props']['recptions'];

    expect($receptions)->toHaveCount(3);
});

test('voucher payment blocked when reception disallows it', function () {
    $reception = Reception::factory()->create(['is_allowed_to_pay_voucher' => false]);
    $user = User::factory()->create();

    $closing = Closing::factory()->create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'open',
    ]);

    $this->actingAs($user);

    $response = $this->post(route('transaction-store'), [
        'income_or_expense' => 'EXPENSE',
        'type' => 'VOUCHER-PAY',
        'voucher_id' => 999,
    ]);

    $response->assertSessionHasErrors('message');
});

test('petty cash payment blocked when reception disallows it', function () {
    $reception = Reception::factory()->create(['is_allowed_to_pay_from_petty_cash' => false]);
    $user = User::factory()->create();

    $closing = Closing::factory()->create([
        'reception_id' => $reception->id,
        'receptionist_id' => $user->id,
        'status' => 'open',
    ]);

    $this->actingAs($user);

    $response = $this->post(route('transaction-store'), [
        'income_or_expense' => 'EXPENSE',
        'type' => 'EXP',
        'amount' => 100,
        'category_id' => 1,
    ]);

    $response->assertSessionHasErrors('message');
});

test('receptionist model has reception relationship', function () {
    $reception = Reception::factory()->create();
    $receptionist = Receptionist::factory()->create(['reception_id' => $reception->id]);

    expect($receptionist->reception)->not->toBeNull();
    expect($receptionist->reception->id)->toBe($reception->id);
});

test('reception model no longer has cash card cheque columns', function () {
    $reception = Reception::factory()->create();

    expect($reception->getAttributes())->not->toHaveKey('is_cash_allowed');
    expect($reception->getAttributes())->not->toHaveKey('is_cheques_allowed');
    expect($reception->getAttributes())->not->toHaveKey('is_card_allowed');
});
