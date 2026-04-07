<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('lookup endpoint returns expected structure', function () {
    $response = $this->getJson('/api/lookup');

    $response->assertOk()
        ->assertJsonStructure(['results', 'keyWord', 'strlen']);
});

test('unauthenticated users cannot access lookup', function () {
    auth()->logout();

    $this->getJson('/api/lookup')->assertUnauthorized();
});
