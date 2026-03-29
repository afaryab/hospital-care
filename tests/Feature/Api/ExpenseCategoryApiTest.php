<?php

use App\Models\ExpenseCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('expense category search returns expected structure', function () {
    ExpenseCategory::factory(3)->create();

    $response = $this->postJson('/api/expense-categories/search', []);

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('unauthenticated users cannot search expense categories', function () {
    auth()->logout();

    $this->postJson('/api/expense-categories/search', [])->assertUnauthorized();
});
