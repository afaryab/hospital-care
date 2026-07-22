<?php

use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('unauthenticated users cannot search drugs', function () {
    auth()->logout();

    $this->getJson('/api/drugs/search?q=amox')->assertUnauthorized();
});

test('empty query returns no results without querying the database', function () {
    Drug::factory()->create(['name' => 'Amoxicillin']);

    $response = $this->getJson('/api/drugs/search?q=');

    $response->assertOk()->assertExactJson(['data' => []]);
});

test('search matches drug by name', function () {
    Drug::factory()->create(['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin Trihydrate']);
    Drug::factory()->create(['name' => 'Paracetamol', 'generic_name' => 'Acetaminophen']);

    $response = $this->getJson('/api/drugs/search?q=amox');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.name'))->toBe('Amoxicillin');
});

test('search matches drug by generic name', function () {
    Drug::factory()->create(['name' => 'Panadol', 'generic_name' => 'Paracetamol']);
    Drug::factory()->create(['name' => 'Amoxil', 'generic_name' => 'Amoxicillin']);

    $response = $this->getJson('/api/drugs/search?q=paracet');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.name'))->toBe('Panadol');
});

test('search excludes inactive drugs', function () {
    Drug::factory()->inactive()->create(['name' => 'Amoxicillin']);

    $response = $this->getJson('/api/drugs/search?q=amox');

    $response->assertOk()->assertExactJson(['data' => []]);
});

test('search results include the drug category', function () {
    $category = DrugCategory::factory()->create(['name' => 'Antibiotics']);
    Drug::factory()->create(['name' => 'Amoxicillin', 'drug_category_id' => $category->id]);

    $response = $this->getJson('/api/drugs/search?q=amox');

    $response->assertOk();
    expect($response->json('data.0.category.name'))->toBe('Antibiotics');
});

test('search respects the limit parameter up to a cap of 50', function () {
    Drug::factory()->count(5)->create(['name' => fn () => 'Amoxicillin '.fake()->unique()->word()]);

    $response = $this->getJson('/api/drugs/search?q=amox&limit=2');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});
