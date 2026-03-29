<?php

use App\Models\ExpenseVoucher;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('expense vouchers search returns json with data structure', function () {
    ExpenseVoucher::factory()->count(2)->create();

    $this->postJson(route('api-expense-vouchers-search'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('expense vouchers search filters by amount range', function () {
    ExpenseVoucher::factory()->create(['amount' => 100]);
    ExpenseVoucher::factory()->create(['amount' => 500]);
    ExpenseVoucher::factory()->create(['amount' => 2000]);

    $response = $this->postJson(route('api-expense-vouchers-search'), [
        'amount_min' => 200,
        'amount_max' => 1000,
    ])->assertOk();

    $possible = $response->json('data.possible');
    foreach ($possible as $v) {
        expect((float) $v['amount'])->toBeGreaterThanOrEqual(200)
            ->and((float) $v['amount'])->toBeLessThanOrEqual(1000);
    }
});

test('expense vouchers search filters by payed_to_name', function () {
    ExpenseVoucher::factory()->create(['payed_to_name' => 'Dr. Smith']);
    ExpenseVoucher::factory()->create(['payed_to_name' => 'XYZ Supplier']);

    $response = $this->postJson(route('api-expense-vouchers-search'), [
        'payed_to_name' => 'Dr. Smith',
    ])->assertOk();

    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('payed_to_name');
    expect($names)->toContain('Dr. Smith');
});

test('expense vouchers search exact match by vc_number', function () {
    $voucher = ExpenseVoucher::factory()->create(['vc_number' => 'VC/2026/03/0001']);
    ExpenseVoucher::factory()->create();

    $response = $this->postJson(route('api-expense-vouchers-search'), [
        'vc_number' => 'VC/2026/03/0001',
    ])->assertOk();

    $exact = $response->json('data.exact');
    expect(collect($exact)->pluck('vc_number'))->toContain('VC/2026/03/0001');
});

test('expense vouchers search respects limit parameter', function () {
    ExpenseVoucher::factory()->count(10)->create();

    $response = $this->postJson(route('api-expense-vouchers-search'), ['limit' => 3])
        ->assertOk();

    expect(count($response->json('data.possible')))->toBeLessThanOrEqual(3);
});

test('expense vouchers search validates limit bounds', function () {
    $this->postJson(route('api-expense-vouchers-search'), ['limit' => 0])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['limit']);
});
