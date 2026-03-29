<?php

namespace Database\Factories;

use App\Enum\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        return [
            'asset_number' => sprintf('AST/%s/%04d', now()->format('Y'), $sequence),
            'name' => fake()->words(3, true),
            'category_id' => AssetCategory::factory(),
            'serial_number' => fake()->bothify('SN-####-??'),
            'purchase_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'purchase_cost' => fake()->randomFloat(2, 1000, 500000),
            'vendor_name' => fake()->company(),
            'status' => AssetStatus::Active->value,
        ];
    }

    public function underMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::UnderMaintenance->value,
        ]);
    }

    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::Retired->value,
        ]);
    }
}
