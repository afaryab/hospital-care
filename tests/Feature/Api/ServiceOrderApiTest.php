<?php

use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// --- Search ---

test('service order search returns expected structure', function () {
    ServiceOrder::factory(3)->create();

    $response = $this->postJson('/api/service-orders/search', []);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('service order search filters by type', function () {
    ServiceOrder::factory()->create(['type' => 'OPD']);
    ServiceOrder::factory()->create(['type' => 'LAB']);

    $response = $this->postJson('/api/service-orders/search', ['type' => 'OPD']);

    $response->assertOk();
    collect($response->json('data.possible'))->each(fn ($item) => expect($item['type'])->toBe('OPD'));
});

test('service order search filters by patient_id', function () {
    $patient = Patient::factory()->create();
    ServiceOrder::factory()->create(['patient_id' => $patient->id]);
    ServiceOrder::factory()->create();

    $response = $this->postJson('/api/service-orders/search', ['patient_id' => $patient->id]);

    $response->assertOk();
    collect($response->json('data.possible'))->each(fn ($item) => expect($item['patient_id'])->toBe($patient->id));
});

test('service order search returns exact match by so_number', function () {
    $order = ServiceOrder::factory()->create();
    $soNumber = $order->so_number;

    $response = $this->postJson('/api/service-orders/search', ['so_number' => $soNumber]);

    $response->assertOk();
    $exact = $response->json('data.exact');
    expect($exact)->not->toBeEmpty()
        ->and($exact[0]['so_number'])->toBe($soNumber);
});

test('service order search filters by date range', function () {
    ServiceOrder::factory()->create(['created_at' => now()->subDays(5)]);
    ServiceOrder::factory()->create(['created_at' => now()->subDays(30)]);

    $response = $this->postJson('/api/service-orders/search', [
        'created_from' => now()->subDays(10)->toDateString(),
        'created_to' => now()->toDateString(),
    ]);

    $response->assertOk();
    expect(count($response->json('data.possible')))->toBeGreaterThanOrEqual(1);
});

test('service order search respects limit', function () {
    ServiceOrder::factory(20)->create();

    $response = $this->postJson('/api/service-orders/search', ['limit' => 3]);

    $response->assertOk();
    expect(count($response->json('data.possible')))->toBeLessThanOrEqual(3);
});

test('service order search validates limit max', function () {
    $response = $this->postJson('/api/service-orders/search', ['limit' => 100]);

    $response->assertUnprocessable();
});

test('service order search validates patient_id exists', function () {
    $response = $this->postJson('/api/service-orders/search', ['patient_id' => 99999]);

    $response->assertUnprocessable();
});

test('unauthenticated users cannot search service orders', function () {
    auth()->logout();

    $this->postJson('/api/service-orders/search', [])->assertUnauthorized();
});

// --- CompletedUnpaid ---

test('completed unpaid returns expected structure', function () {
    ServiceOrder::factory()->create(['status' => 'CLOSED']);

    $response = $this->postJson('/api/service-orders/completed-unpaid', []);

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('completed unpaid only returns closed service orders', function () {
    ServiceOrder::factory()->create(['status' => 'CLOSED']);
    ServiceOrder::factory()->create(['status' => 'OPEN']);

    $response = $this->postJson('/api/service-orders/completed-unpaid', []);

    $response->assertOk();
    collect($response->json('data'))->each(fn ($item) => expect($item['status'])->toBe('CLOSED'));
});

test('completed unpaid respects limit', function () {
    ServiceOrder::factory(20)->create(['status' => 'CLOSED']);

    $response = $this->postJson('/api/service-orders/completed-unpaid', ['limit' => 5]);

    $response->assertOk();
    expect(count($response->json('data')))->toBeLessThanOrEqual(5);
});

test('completed unpaid validates limit range', function () {
    $response = $this->postJson('/api/service-orders/completed-unpaid', ['limit' => 200]);

    $response->assertUnprocessable();
});

test('completed unpaid filters by search term', function () {
    $order = ServiceOrder::factory()->create(['status' => 'CLOSED']);
    $soNumber = $order->so_number;

    $response = $this->postJson('/api/service-orders/completed-unpaid', ['search' => $soNumber]);

    $response->assertOk();
    $data = $response->json('data');
    expect(collect($data)->pluck('so_number')->contains(fn ($n) => str_contains($n, $soNumber)))->toBeTrue();
});

test('unauthenticated users cannot access completed unpaid', function () {
    auth()->logout();

    $this->postJson('/api/service-orders/completed-unpaid', [])->assertUnauthorized();
});
