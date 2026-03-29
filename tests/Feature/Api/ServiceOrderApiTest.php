<?php

use App\Models\ServiceOrder;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('service order search returns expected structure', function () {
    ServiceOrder::factory(3)->create();

    $response = $this->postJson('/api/service-orders/search', []);

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('service order search filters by type', function () {
    ServiceOrder::factory()->create(['type' => 'OPD']);
    ServiceOrder::factory()->create(['type' => 'LAB']);

    $response = $this->postJson('/api/service-orders/search', ['type' => 'OPD']);

    $response->assertOk();
    collect($response->json('data'))->each(fn ($item) => expect($item['type'])->toBe('OPD'));
});

test('unauthenticated users cannot search service orders', function () {
    auth()->logout();

    $this->postJson('/api/service-orders/search', [])->assertUnauthorized();
});
