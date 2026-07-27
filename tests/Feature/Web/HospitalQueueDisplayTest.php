<?php

use App\Models\ServiceOrder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('emergency queue display shows EMG service orders', function () {
    actingAs(User::factory()->create());

    $order = ServiceOrder::factory()->create(['type' => 'EMG', 'status' => 'open']);

    get(route('hospital-emergency-queue'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('hospital/opd-queue')
            ->has("serviceOrdersByService.{$order->service_id}", 1)
        );
});

test('laboratory queue display shows PTH service orders, not dental', function () {
    actingAs(User::factory()->create());

    $labOrder = ServiceOrder::factory()->create(['type' => 'PTH', 'status' => 'open']);
    $dentalOrder = ServiceOrder::factory()->create(['type' => 'DNT', 'status' => 'open']);

    get(route('hospital-laboratory-queue'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('hospital/opd-queue')
            ->has("serviceOrdersByService.{$labOrder->service_id}", 1)
            ->missing("serviceOrdersByService.{$dentalOrder->service_id}")
        );
});
