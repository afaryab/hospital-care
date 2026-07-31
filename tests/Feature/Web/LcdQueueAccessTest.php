<?php

use App\Models\LcdOpdOperator;
use App\Models\LcdXrayOperator;
use App\Models\ServiceOrder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('an LCD operator can view their own department queue', function () {
    $user = User::factory()->create();
    LcdOpdOperator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    get(route('hospital-opd-queue'))->assertOk();
});

test('an LCD operator is blocked from other departments queues', function () {
    $user = User::factory()->create();
    LcdOpdOperator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    get(route('hospital-emergency-queue'))->assertForbidden();
    get(route('hospital-indoor-queue'))->assertForbidden();
    get(route('hospital-dental-queue'))->assertForbidden();
    get(route('hospital-laboratory-queue'))->assertForbidden();
    get(route('hospital-ultrasound-queue'))->assertForbidden();
    get(route('hospital-radiology-queue'))->assertForbidden();
});

test('a user with no LCD profile is not restricted on any queue route', function () {
    actingAs(User::factory()->create());

    get(route('hospital-opd-queue'))->assertOk();
    get(route('hospital-emergency-queue'))->assertOk();
    get(route('hospital-radiology-queue'))->assertOk();
});

test('radiology queue display shows orders of the canonical XRAY type', function () {
    $user = User::factory()->create();
    LcdXrayOperator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    $xrayOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'status' => 'open']);
    $legacyRadOrder = ServiceOrder::factory()->create(['type' => 'RAD', 'status' => 'open']);
    $otherOrder = ServiceOrder::factory()->create(['type' => 'OPD', 'status' => 'open']);

    get(route('hospital-radiology-queue'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('hospital/opd-queue')
            ->has("serviceOrdersByService.{$xrayOrder->service_id}", 1)
            ->has("serviceOrdersByService.{$legacyRadOrder->service_id}", 1)
            ->missing("serviceOrdersByService.{$otherOrder->service_id}")
        );
});
