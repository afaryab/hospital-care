<?php

use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\ServiceOrder;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('voucher can be created for a service order regardless of status', function () {
    $category = ExpenseCategory::factory()->create([
        'pay_doc' => true,
        'pay_others' => false,
        'pay_users' => false,
        'allow_voucher' => true,
    ]);
    $doctor = User::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create(['status' => 'open']);

    $response = $this->post(route('counter-expense-store-voucher'), [
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
        'service_order_ids' => [$serviceOrder->id],
        'amount' => 500,
        'description' => 'Doctor payout',
    ]);

    $response->assertRedirect(route('counter-expense'));
    $response->assertSessionHasNoErrors();

    assertDatabaseHas(ExpenseVoucher::class, [
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
        'amount' => 500,
    ]);

    $voucher = ExpenseVoucher::where('payed_to', $doctor->id)->first();
    expect($voucher->serviceOrders->pluck('id'))->toContain($serviceOrder->id);
});

test('voucher can be created for a service order that already has a voucher', function () {
    $category = ExpenseCategory::factory()->create([
        'pay_doc' => true,
        'pay_others' => false,
        'pay_users' => false,
        'allow_voucher' => true,
    ]);
    $doctor = User::factory()->create();
    $serviceOrder = ServiceOrder::factory()->create(['status' => 'CLOSED']);

    $existingVoucher = ExpenseVoucher::factory()->create([
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
    ]);
    $existingVoucher->serviceOrders()->attach($serviceOrder->id);

    $response = $this->post(route('counter-expense-store-voucher'), [
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
        'service_order_ids' => [$serviceOrder->id],
        'amount' => 250,
    ]);

    $response->assertRedirect(route('counter-expense'));
    $response->assertSessionHasNoErrors();

    expect(ExpenseVoucher::where('payed_to', $doctor->id)->count())->toBe(2);
});

test('voucher creation still requires service orders for doctor-only categories', function () {
    $category = ExpenseCategory::factory()->create([
        'pay_doc' => true,
        'pay_others' => false,
        'pay_users' => false,
        'allow_voucher' => true,
    ]);
    $doctor = User::factory()->create();

    $response = $this->post(route('counter-expense-store-voucher'), [
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
        'service_order_ids' => [],
        'amount' => 500,
    ]);

    $response->assertSessionHasErrors('service_order_ids');
});

test('voucher creation is rejected for categories that do not allow vouchers', function () {
    $category = ExpenseCategory::factory()->create(['allow_voucher' => false]);
    $doctor = User::factory()->create();

    $response = $this->post(route('counter-expense-store-voucher'), [
        'exp_category_id' => $category->id,
        'payed_to' => $doctor->id,
        'amount' => 500,
    ]);

    $response->assertSessionHasErrors('exp_category_id');
});
