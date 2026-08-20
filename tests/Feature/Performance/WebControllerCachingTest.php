<?php

use App\Models\Closing;
use App\Models\ExpenseCategory;
use App\Models\Panel;
use App\Models\PaymentMethod;
use App\Models\ServiceDepartment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Cache::flush();
});

test('the patient register page populates the ServiceDepartment cache instead of querying every request', function () {
    actingAs(User::factory()->create());

    expect(Cache::has(ServiceDepartment::cacheKey()))->toBeFalse();

    get(route('patients-register'))->assertOk();

    expect(Cache::has(ServiceDepartment::cacheKey()))->toBeTrue();
});

test('the counter select-patient page populates the ServiceDepartment cache', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    get(route('counter-select-patient'))->assertOk();

    expect(Cache::has(ServiceDepartment::cacheKey()))->toBeTrue();
});

test('the receivables page populates the PaymentMethod and Panel caches', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    get(route('receaveables'))->assertOk();

    expect(Cache::has(PaymentMethod::cacheKey()))->toBeTrue()
        ->and(Cache::has(Panel::cacheKey()))->toBeTrue();
});

test('the counter expense page populates the ExpenseCategory cache', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    get(route('counter-expense'))->assertOk();

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();
});

test('the expense category cache serves correctly filtered results on the counter expense page', function () {
    $user = User::factory()->create();
    Closing::factory()->create(['status' => 'open', 'receptionist_id' => $user->id]);
    actingAs($user);

    ExpenseCategory::factory()->create(['name' => 'Petty Cash Category', 'allow_petty_cash' => true]);
    ExpenseCategory::factory()->create(['name' => 'Not Petty Cash', 'allow_petty_cash' => false]);

    // Warm the cache under a stale value first — if the controller reads
    // straight from Cacheable's shared listing and filters in-memory, the
    // subsequent request still filters correctly on cache hit, not just on
    // the first (cache-miss) request.
    ExpenseCategory::cachedAll();

    $response = get(route('counter-expense'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('categories', 1)
        ->where('categories.0.name', 'Petty Cash Category'));
});

test('new-voucher pages populate the ExpenseCategory cache', function () {
    actingAs(User::factory()->create());

    get(route('counter-expense-new-voucher'))->assertOk();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();

    Cache::flush();
    get(route('counter-expense-new-doctor-voucher'))->assertOk();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();

    Cache::flush();
    get(route('counter-expense-new-user-voucher'))->assertOk();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();
});
