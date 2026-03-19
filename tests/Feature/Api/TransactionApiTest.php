<?php

use App\Models\Patient;
use App\Models\Transaction;

test('transactions search returns json with data structure', function () {
    Transaction::factory()->count(2)->create();

    $this->postJson(route('api-transactions-search'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['exact', 'possible']]);
});

test('transactions search filters by income_or_expense INCOME', function () {
    Transaction::factory()->create(['income_or_expense' => 'INCOME']);
    Transaction::factory()->expense()->create();

    $response = $this->postJson(route('api-transactions-search'), [
        'income_or_expense' => 'INCOME',
    ])->assertOk();

    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('income_or_expense')->unique()->values()->all())->toBe(['INCOME']);
});

test('transactions search filters by income_or_expense EXPENSE', function () {
    Transaction::factory()->create(['income_or_expense' => 'INCOME']);
    Transaction::factory()->expense()->create();

    $response = $this->postJson(route('api-transactions-search'), [
        'income_or_expense' => 'EXPENSE',
    ])->assertOk();

    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('income_or_expense')->unique()->values()->all())->toBe(['EXPENSE']);
});

test('transactions search filters by patient_id', function () {
    $patient = Patient::factory()->create();
    $transaction = Transaction::factory()->create(['patient_id' => $patient->id]);
    Transaction::factory()->create(); // different patient

    $response = $this->postJson(route('api-transactions-search'), [
        'patient_id' => $patient->id,
    ])->assertOk();

    $possible = $response->json('data.possible');
    expect(collect($possible)->pluck('patient_id')->unique()->values()->all())->toBe([$patient->id]);
});

test('transactions search exact match by tr_number', function () {
    $transaction = Transaction::factory()->create(['tr_number' => 'TR/2026/03/19/0001']);
    Transaction::factory()->create();

    $response = $this->postJson(route('api-transactions-search'), [
        'tr_number' => 'TR/2026/03/19/0001',
    ])->assertOk();

    $exact = $response->json('data.exact');
    expect(collect($exact)->pluck('tr_number'))->toContain('TR/2026/03/19/0001');
});

test('transactions search validates income_or_expense value', function () {
    $this->postJson(route('api-transactions-search'), [
        'income_or_expense' => 'INVALID',
    ])->assertStatus(422)
      ->assertJsonValidationErrors(['income_or_expense']);
});

test('transactions search filters by amount range', function () {
    Transaction::factory()->create(['amount' => 500]);
    Transaction::factory()->create(['amount' => 2000]);
    Transaction::factory()->create(['amount' => 5000]);

    $response = $this->postJson(route('api-transactions-search'), [
        'amount_min' => 1000,
        'amount_max' => 3000,
    ])->assertOk();

    $possible = $response->json('data.possible');
    foreach ($possible as $tx) {
        expect((float) $tx['amount'])->toBeGreaterThanOrEqual(1000)
            ->and((float) $tx['amount'])->toBeLessThanOrEqual(3000);
    }
});

test('transactions search respects limit parameter', function () {
    Transaction::factory()->count(15)->create();

    $response = $this->postJson(route('api-transactions-search'), ['limit' => 5])
        ->assertOk();

    expect(count($response->json('data.possible')))->toBeLessThanOrEqual(5);
});
