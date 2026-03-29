<?php

use App\Models\Closing;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('closings search returns json with data structure', function () {
    Closing::factory()->count(2)->create();

    $this->postJson(route('api-closings-search'))
        ->assertOk()
        ->assertJsonStructure(['data']);
});

test('closings search filters by status open', function () {
    Closing::factory()->create(['status' => 'open']);
    Closing::factory()->create(['status' => 'closed']);

    $response = $this->postJson(route('api-closings-search'), ['status' => 'open'])
        ->assertOk();

    $data = $response->json('data');
    expect(collect($data)->pluck('status')->unique()->values()->all())->toBe(['open']);
});

test('closings search filters by status closed', function () {
    Closing::factory()->create(['status' => 'open']);
    Closing::factory()->create(['status' => 'closed']);

    $response = $this->postJson(route('api-closings-search'), ['status' => 'closed'])
        ->assertOk();

    $data = $response->json('data');
    expect(collect($data)->pluck('status')->unique()->values()->all())->toBe(['closed']);
});

test('closings search filters by ct_number search term', function () {
    $closing = Closing::factory()->create(['ct_number' => 'CT/2026/03/0001']);
    Closing::factory()->create(['ct_number' => 'CT/2025/01/0001']);

    $response = $this->postJson(route('api-closings-search'), ['search' => 'CT/2026'])
        ->assertOk();

    $data = $response->json('data');
    $ctNumbers = collect($data)->pluck('ct_number');
    expect($ctNumbers)->toContain('CT/2026/03/0001');
});

test('closings search validates invalid status', function () {
    $this->postJson(route('api-closings-search'), ['status' => 'invalid'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('closings search respects limit parameter', function () {
    Closing::factory()->count(10)->create();

    $response = $this->postJson(route('api-closings-search'), ['limit' => 3])
        ->assertOk();

    expect(count($response->json('data')))->toBeLessThanOrEqual(3);
});
