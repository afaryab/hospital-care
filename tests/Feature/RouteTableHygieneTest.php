<?php

use App\Models\Closing;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('viewing the counter-close confirmation page (GET) does not write to the database', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->create([
        'status' => 'open',
        'receptionist_id' => $user->id,
        'closing_amount' => 0,
        'expense_payed' => 0,
    ]);
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 500,
    ]);

    actingAs($user);

    $updatedAtBefore = Closing::find($closing->id)->updated_at;

    get(route('counter-close'))->assertOk();

    $updatedAtAfter = Closing::find($closing->id)->updated_at;

    expect($updatedAtAfter->equalTo($updatedAtBefore))->toBeTrue();
});

test('the counter-close confirmation page still displays correct computed totals', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->create([
        'status' => 'open',
        'receptionist_id' => $user->id,
    ]);
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 700,
    ]);
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'EXPENSE',
        'amount' => 200,
    ]);

    actingAs($user);

    get(route('counter-close'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('openCounter.closing_amount', 500)
            ->where('openCounter.expense_payed', 200));
});

test('actually closing the counter (POST) still persists correctly', function () {
    $user = User::factory()->create();
    $closing = Closing::factory()->create([
        'status' => 'open',
        'receptionist_id' => $user->id,
    ]);
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 300,
    ]);

    actingAs($user);

    $this->post(route('counter-close'))->assertRedirect();

    $closing->refresh();
    expect($closing->status)->toBe('CLOSED')
        ->and($closing->closed_at)->not->toBeNull();
});

test('the closings/search API route is registered exactly once', function () {
    $routes = collect(app('router')->getRoutes())
        ->filter(fn ($route) => $route->getName() === 'api-closings-search');

    expect($routes)->toHaveCount(1);
});
