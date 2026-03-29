<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetDepreciationEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetDepreciationEntryFactory extends Factory
{
    protected $model = AssetDepreciationEntry::class;

    public function definition(): array
    {
        $purchasePrice = fake()->numberBetween(50000, 500000);
        $depreciation = round($purchasePrice * 0.0125, 2); // ~15% annual / 12 months
        $accumulated = fake()->numberBetween(0, (int) $purchasePrice);
        $bookValue = max(0, $purchasePrice - $accumulated);

        return [
            'asset_id' => Asset::factory(),
            'period_year' => fake()->numberBetween(2023, 2026),
            'period_month' => fake()->numberBetween(1, 12),
            'depreciation_amount' => $depreciation,
            'accumulated_depreciation' => $accumulated,
            'book_value' => $bookValue,
        ];
    }
}
