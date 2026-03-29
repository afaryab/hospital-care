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
        ->assertJsonStructure(['data']);
});

test('user search filters by name', function () {
    User::factory()->create(['name' => 'Dr. Ahmed Khan']);

    $response = $this->postJson('/api/users/search', ['name' => 'Ahmed']);

    $response->assertOk();
    expect($response->json('data'))->not->toBeEmpty();
});

test('unauthenticated users cannot search users', function () {
    auth()->logout();

    $this->postJson('/api/users/search', [])->assertUnauthorized();
});
