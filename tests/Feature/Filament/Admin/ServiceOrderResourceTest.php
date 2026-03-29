<?php

use App\Filament\Admin\Resources\ServiceOrders\Pages\ListServiceOrders;
use App\Filament\Admin\Resources\ServiceOrders\Pages\ViewServiceOrder;
use App\Models\Administrator;
use App\Models\ServiceOrder;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('service order list page renders', function () {
    Livewire\Livewire::test(ListServiceOrders::class)->assertSuccessful();
});

test('service order view page renders', function () {
    $serviceOrder = ServiceOrder::factory()->create();
    Livewire\Livewire::test(ViewServiceOrder::class, ['record' => $serviceOrder->getRouteKey()])->assertSuccessful();
});
