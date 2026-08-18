<?php

use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('cachedAll caches its result under the model cache key', function () {
    ExpenseCategory::factory()->count(3)->create();

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();

    $result = ExpenseCategory::cachedAll();

    expect($result)->toHaveCount(3)
        ->and(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();
});

test('cachedAll returns the cached value rather than re-querying', function () {
    ExpenseCategory::factory()->create();
    ExpenseCategory::cachedAll();

    // Plant a sentinel directly in the cache — if cachedAll() reads from
    // cache rather than the database, it comes back untouched.
    Cache::put(ExpenseCategory::cacheKey(), collect(['sentinel']), 60);

    expect(ExpenseCategory::cachedAll()->all())->toBe(['sentinel']);
});

test('creating a record flushes the cache', function () {
    ExpenseCategory::cachedAll();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();

    ExpenseCategory::factory()->create();

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();
});

test('updating a record flushes the cache', function () {
    $category = ExpenseCategory::factory()->create();
    ExpenseCategory::cachedAll();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();

    $category->update(['name' => 'Renamed Category']);

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();
});

test('deleting a record flushes the cache', function () {
    $category = ExpenseCategory::factory()->create();
    ExpenseCategory::cachedAll();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();

    $category->delete();

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();
});

test('a subsequent read after a flush rebuilds the cache from the database', function () {
    ExpenseCategory::factory()->create(['name' => 'Original']);
    ExpenseCategory::cachedAll();

    ExpenseCategory::flushCache();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();

    $fresh = ExpenseCategory::cachedAll();
    expect($fresh->pluck('name')->all())->toBe(['Original'])
        ->and(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();
});

test('flushCache does not throw when the cache store is unreachable', function () {
    Cache::partialMock()->shouldReceive('forget')->once()->andThrow(new RuntimeException('connection refused'));

    ExpenseCategory::flushCache();

    expect(true)->toBeTrue(); // reaching here means the exception was swallowed
});

test('rememberCache falls back to a direct query when the cache store is unreachable', function () {
    ExpenseCategory::factory()->create();

    Cache::partialMock()->shouldReceive('remember')->once()->andThrow(new RuntimeException('connection refused'));

    expect(ExpenseCategory::cachedAll())->toHaveCount(1);
});
