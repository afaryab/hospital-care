<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user search returns expected structure', function () {
    User::factory(3)->create();

    $response = $this->postJson('/api/users/search', []);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('user search filters by name', function () {
    User::factory()->create(['name' => 'Dr. Ahmed Khan']);
    User::factory()->create(['name' => 'Jane Doe']);

    $response = $this->postJson('/api/users/search', ['name' => 'Ahmed']);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('name')->contains(fn ($n) => str_contains($n, 'Ahmed')))->toBeTrue();
});

test('user search returns exact match separately', function () {
    $user = User::factory()->create(['name' => 'Exact User Name']);

    $response = $this->postJson('/api/users/search', ['name' => 'Exact User Name']);

    $response->assertOk();
    $exact = $response->json('data.exact');
    expect($exact)->not->toBeEmpty()
        ->and($exact[0]['name'])->toBe('Exact User Name');
});

test('user search filters by username', function () {
    User::factory()->create(['username' => 'docahmed', 'name' => 'Dr Ahmed']);
    User::factory()->create(['username' => 'janedoe', 'name' => 'Jane Doe']);

    $response = $this->postJson('/api/users/search', ['username' => 'docahmed']);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('username')->contains(fn ($u) => str_contains($u, 'docahmed')))->toBeTrue();
});

test('user search filters by email', function () {
    User::factory()->create(['email' => 'dr.ahmed@hospital.com', 'name' => 'Dr Ahmed']);

    $response = $this->postJson('/api/users/search', ['email' => 'dr.ahmed']);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('email')->contains(fn ($e) => str_contains($e, 'dr.ahmed')))->toBeTrue();
});

test('user search filters by is_active', function () {
    User::factory()->create(['is_active' => true, 'name' => 'Active User One']);
    User::factory()->create(['is_active' => false, 'name' => 'Inactive User']);

    $response = $this->postJson('/api/users/search', ['is_active' => true]);

    $response->assertOk();
    // The response filters by is_active but the returned select only includes id/name/username/email
    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    expect($names->contains('Active User One'))->toBeTrue()
        ->and($names->contains('Inactive User'))->toBeFalse();
});

test('user search respects limit', function () {
    User::factory(20)->create();

    $response = $this->postJson('/api/users/search', ['limit' => 3]);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(count($possible))->toBeLessThanOrEqual(3);
});

test('user search validates limit range', function () {
    $response = $this->postJson('/api/users/search', ['limit' => 200]);

    $response->assertUnprocessable();
});

test('user search with no filters returns all users up to default limit', function () {
    User::factory(5)->create();

    $response = $this->postJson('/api/users/search', []);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(count($possible))->toBeGreaterThan(0);
});

test('unauthenticated users cannot search users', function () {
    auth()->logout();

    $this->postJson('/api/users/search', [])->assertUnauthorized();
});
