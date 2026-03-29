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
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('expense category search filters by name', function () {
    ExpenseCategory::factory()->create(['name' => 'Medical Equipment']);
    ExpenseCategory::factory()->create(['name' => 'Office Supplies']);

    $response = $this->postJson('/api/expense-categories/search', ['name' => 'Medical']);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('name')->contains(fn ($n) => str_contains($n, 'Medical')))->toBeTrue();
});

test('expense category search returns exact match separately', function () {
    $category = ExpenseCategory::factory()->create(['name' => 'Exact Match Category']);

    $response = $this->postJson('/api/expense-categories/search', ['name' => 'Exact Match Category']);

    $response->assertOk();
    $exact = $response->json('data.exact');
    expect($exact)->not->toBeEmpty()
        ->and($exact[0]['name'])->toBe('Exact Match Category');
});

test('expense category search filters by type', function () {
    ExpenseCategory::factory()->create(['type' => 'OPD', 'name' => 'OPD Category']);
    ExpenseCategory::factory()->create(['type' => 'LAB', 'name' => 'LAB Category']);

    $response = $this->postJson('/api/expense-categories/search', ['type' => 'OPD']);

    $response->assertOk();
    $possible = $response->json('data.possible');
    collect($possible)->each(fn ($item) => expect($item['type'])->toBe('OPD'));
});

test('expense category search filters by pay_doc flag', function () {
    ExpenseCategory::factory()->create(['pay_doc' => true, 'name' => 'Doc Pay Cat']);
    ExpenseCategory::factory()->create(['pay_doc' => false, 'name' => 'No Doc Pay Cat']);

    $response = $this->postJson('/api/expense-categories/search', ['pay_doc' => true]);

    $response->assertOk();
    $possible = $response->json('data.possible');
    collect($possible)->each(fn ($item) => expect($item['pay_doc'])->toBeTrue());
});

test('expense category search filters by pay_others flag', function () {
    ExpenseCategory::factory()->create(['pay_others' => true, 'name' => 'Others Pay Cat']);
    ExpenseCategory::factory()->create(['pay_others' => false, 'name' => 'No Others Pay Cat']);

    $response = $this->postJson('/api/expense-categories/search', ['pay_others' => true]);

    $response->assertOk();
    $possible = $response->json('data.possible');
    collect($possible)->each(fn ($item) => expect($item['pay_others'])->toBeTrue());
});

test('expense category search filters by pay_users flag', function () {
    ExpenseCategory::factory()->create(['pay_users' => true, 'name' => 'Users Pay Cat']);
    ExpenseCategory::factory()->create(['pay_users' => false, 'name' => 'No Users Pay Cat']);

    $response = $this->postJson('/api/expense-categories/search', ['pay_users' => true]);

    $response->assertOk();
    $possible = $response->json('data.possible');
    collect($possible)->each(fn ($item) => expect($item['pay_users'])->toBeTrue());
});

test('expense category search respects limit', function () {
    ExpenseCategory::factory(20)->create();

    $response = $this->postJson('/api/expense-categories/search', ['limit' => 5]);

    $response->assertOk();
    $possible = $response->json('data.possible');
    expect(count($possible))->toBeLessThanOrEqual(5);
});

test('expense category search excludes reserved system categories', function () {
    ExpenseCategory::factory()->create(['name' => 'Outdoor Doctors Payments']);
    ExpenseCategory::factory()->create(['name' => 'Indoor Doctors Payments']);
    ExpenseCategory::factory()->create(['name' => 'Regular Category']);

    $response = $this->postJson('/api/expense-categories/search', []);

    $response->assertOk();
    $possible = $response->json('data.possible');
    $names = collect($possible)->pluck('name');
    expect($names->contains('Outdoor Doctors Payments'))->toBeFalse()
        ->and($names->contains('Indoor Doctors Payments'))->toBeFalse()
        ->and($names->contains('Regular Category'))->toBeTrue();
});

test('expense category search validates limit range', function () {
    $response = $this->postJson('/api/expense-categories/search', ['limit' => 200]);

    $response->assertUnprocessable();
});

test('unauthenticated users cannot search expense categories', function () {
    auth()->logout();

    $this->postJson('/api/expense-categories/search', [])->assertUnauthorized();
});
