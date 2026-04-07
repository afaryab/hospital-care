<?php

use App\Models\ServiceOrder;
use App\Models\TransactionElement;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('authenticated user can open service orders overview and see records', function () {
    $user = User::factory()->create();
    actingAs($user);

    $serviceOrder = ServiceOrder::factory()->create([
        'status' => 'OPEN',
    ]);

    TransactionElement::factory()->create([
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
        'amount' => 1500,
    ]);

    get(route('service-orders-overview'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('service-orders/index')
            ->has('serviceOrders.data', 1)
            ->where('serviceOrders.data.0.id', $serviceOrder->id)
        );
});

test('service orders overview loads selected service order profile details', function () {
    $user = User::factory()->create();
    actingAs($user);

    $serviceOrder = ServiceOrder::factory()->create([
        'status' => 'CLOSED',
    ]);

    TransactionElement::factory()->create([
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
        'amount' => 2000,
    ]);

    TransactionElement::factory()->create([
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 400,
    ]);

    get(route('service-orders-overview', ['service_order_id' => $serviceOrder->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('service-orders/index')
            ->where('selectedServiceOrder.id', $serviceOrder->id)
            ->where('selectedServiceOrder.income_total', 2000)
            ->where('selectedServiceOrder.expense_total', 400)
        );
});
