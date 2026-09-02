<?php

use App\Filament\Admin\Pages\CacheSettings;
use App\Models\Administrator;
use App\Models\ExpenseCategory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
    $this->admin = User::factory()->create();
    Administrator::create(['user_id' => $this->admin->id, 'authority' => 'administrator']);
    $this->actingAs($this->admin);
});

test('cache settings page renders for an admin', function () {
    Livewire\Livewire::test(CacheSettings::class)->assertSuccessful();
});

test('a non-admin cannot access the cache settings page', function () {
    $receptionist = User::factory()->create();

    $this->actingAs($receptionist);

    expect(CacheSettings::canAccess())->toBeFalse();
});

test('clearModelCache flushes the given model and leaves others untouched', function () {
    ExpenseCategory::factory()->create();
    ExpenseCategory::cachedAll();
    Service::cachedActive();

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue()
        ->and(Cache::has(Service::cacheKey()))->toBeTrue();

    Livewire\Livewire::test(CacheSettings::class)
        ->call('clearModelCache', 'expense_categories');

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse()
        ->and(Cache::has(Service::cacheKey()))->toBeTrue();
});

test('warmModelCache populates the cache for the given model', function () {
    ExpenseCategory::factory()->count(2)->create();
    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse();

    Livewire\Livewire::test(CacheSettings::class)
        ->call('warmModelCache', 'expense_categories');

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeTrue();
});

test('the clear all caches header action flushes every cacheable model', function () {
    ExpenseCategory::factory()->create();
    ExpenseCategory::cachedAll();
    Service::cachedActive();

    Livewire\Livewire::test(CacheSettings::class)
        ->callAction('clearAllCaches');

    expect(Cache::has(ExpenseCategory::cacheKey()))->toBeFalse()
        ->and(Cache::has(Service::cacheKey()))->toBeFalse();
});

test('getCollectiveStats reports the driver, cached model count, and cached record count', function () {
    ExpenseCategory::factory()->count(3)->create();
    ExpenseCategory::cachedAll();

    $stats = (new CacheSettings)->getCollectiveStats();

    expect($stats['driver'])->toBe(config('cache.default'))
        ->and($stats['models_cached'])->toBeGreaterThanOrEqual(1)
        ->and($stats['records_cached'])->toBeGreaterThanOrEqual(3);
});
