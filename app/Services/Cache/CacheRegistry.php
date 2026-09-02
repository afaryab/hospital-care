<?php

namespace App\Services\Cache;

use App\Models\AssetCategory;
use App\Models\DrugCategory;
use App\Models\ExpenseCategory;
use App\Models\Icd10Code;
use App\Models\Panel;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\StockCategory;
use App\Models\Triage;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Central list of every model with a Cacheable-backed listing, for the
 * Filament "Cache Settings" page. Adding a new cacheable model means adding
 * one entry here alongside the model's own cachedXxx() method — the page,
 * its stats, and its tests all drive off this list rather than each
 * hardcoding the model set separately.
 */
class CacheRegistry
{
    /**
     * @return list<array{key: string, label: string, model: class-string, cache_key: string, count: \Closure(): int, total: \Closure(): int}>
     */
    public static function entries(): array
    {
        return [
            [
                'key' => 'services',
                'label' => 'Services',
                'model' => Service::class,
                'cache_key' => Service::cacheKey(),
                'warm' => fn () => Service::cachedActive(),
                'total' => fn () => Service::count(),
            ],
            [
                'key' => 'service_departments',
                'label' => 'Service Departments',
                'model' => ServiceDepartment::class,
                'cache_key' => ServiceDepartment::cacheKey(),
                'warm' => fn () => ServiceDepartment::cachedAll(),
                'total' => fn () => ServiceDepartment::count(),
            ],
            [
                'key' => 'icd10_codes',
                'label' => 'ICD-10 Categories',
                'model' => Icd10Code::class,
                'cache_key' => Icd10Code::cacheKey(),
                'warm' => fn () => Icd10Code::cachedCategories(),
                'total' => fn () => Icd10Code::query()->where('is_active', true)->count(),
            ],
            [
                'key' => 'expense_categories',
                'label' => 'Expense Categories',
                'model' => ExpenseCategory::class,
                'cache_key' => ExpenseCategory::cacheKey(),
                'warm' => fn () => ExpenseCategory::cachedAll(),
                'total' => fn () => ExpenseCategory::count(),
            ],
            [
                'key' => 'payment_methods',
                'label' => 'Payment Methods',
                'model' => PaymentMethod::class,
                'cache_key' => PaymentMethod::cacheKey(),
                'warm' => fn () => PaymentMethod::cachedAll(),
                'total' => fn () => PaymentMethod::count(),
            ],
            [
                'key' => 'panels',
                'label' => 'Panels',
                'model' => Panel::class,
                'cache_key' => Panel::cacheKey(),
                'warm' => fn () => Panel::cachedActive(),
                'total' => fn () => Panel::query()->where('is_active', true)->count(),
            ],
            [
                'key' => 'triages',
                'label' => 'Triage Levels',
                'model' => Triage::class,
                'cache_key' => Triage::cacheKey(),
                'warm' => fn () => Triage::cachedActive(),
                'total' => fn () => Triage::query()->where('is_active', true)->count(),
            ],
            [
                'key' => 'stock_categories',
                'label' => 'Stock Categories',
                'model' => StockCategory::class,
                'cache_key' => StockCategory::cacheKey(),
                'warm' => fn () => StockCategory::cachedAll(),
                'total' => fn () => StockCategory::count(),
            ],
            [
                'key' => 'asset_categories',
                'label' => 'Asset Categories',
                'model' => AssetCategory::class,
                'cache_key' => AssetCategory::cacheKey(),
                'warm' => fn () => AssetCategory::cachedAll(),
                'total' => fn () => AssetCategory::count(),
            ],
            [
                'key' => 'drug_categories',
                'label' => 'Drug Categories',
                'model' => DrugCategory::class,
                'cache_key' => DrugCategory::cacheKey(),
                'warm' => fn () => DrugCategory::cachedAll(),
                'total' => fn () => DrugCategory::count(),
            ],
            [
                'key' => 'doctors',
                'label' => 'Doctors / Providers',
                'model' => User::class,
                'cache_key' => User::cacheKey(),
                'warm' => fn () => User::cachedDoctors(),
                'total' => fn () => User::query()
                    ->where(fn ($q) => $q
                        ->whereHas('opdDoctorProfiles')
                        ->orWhereHas('indDoctorProfiles')
                        ->orWhereHas('emergencyDoctorProfiles')
                        ->orWhereHas('dentistProfiles')
                        ->orWhereHas('ultrasoundDoctorProfiles')
                        ->orWhereHas('xrayTechnicianProfiles'))
                    ->count(),
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return collect(self::entries())->firstWhere('key', $key);
    }

    /**
     * @return array{key: string, is_cached: bool, cached_count: int, total_count: int}
     */
    public static function status(array $entry): array
    {
        $cached = Cache::get($entry['cache_key']);

        return [
            'key' => $entry['key'],
            'label' => $entry['label'],
            'is_cached' => $cached !== null,
            'cached_count' => $cached !== null ? self::countOf($cached) : 0,
            'total_count' => ($entry['total'])(),
        ];
    }

    protected static function countOf(mixed $value): int
    {
        if (is_countable($value)) {
            return count($value);
        }

        return $value === null ? 0 : 1;
    }
}
