<?php

use App\Models\ExpenseCategory;
use App\Services\Cache\CacheRegistry;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('entries lists every cacheable model with the required keys', function () {
    $entries = CacheRegistry::entries();

    expect($entries)->not->toBeEmpty();

    foreach ($entries as $entry) {
        expect($entry)->toHaveKeys(['key', 'label', 'model', 'cache_key', 'warm', 'total'])
            ->and(class_exists($entry['model']))->toBeTrue()
            ->and($entry['warm'])->toBeInstanceOf(Closure::class)
            ->and($entry['total'])->toBeInstanceOf(Closure::class);
    }
});

test('find returns the entry matching a key, or null', function () {
    expect(CacheRegistry::find('expense_categories'))->not->toBeNull()
        ->and(CacheRegistry::find('does-not-exist'))->toBeNull();
});

test('status reports uncached until the model is warmed, then reflects the cached count', function () {
    ExpenseCategory::factory()->count(2)->create();
    $entry = CacheRegistry::find('expense_categories');

    $before = CacheRegistry::status($entry);
    expect($before['is_cached'])->toBeFalse()
        ->and($before['cached_count'])->toBe(0)
        ->and($before['total_count'])->toBe(2);

    ($entry['warm'])();

    $after = CacheRegistry::status($entry);
    expect($after['is_cached'])->toBeTrue()
        ->and($after['cached_count'])->toBe(2)
        ->and($after['total_count'])->toBe(2);
});
