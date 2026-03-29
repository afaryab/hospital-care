<?php

namespace Database\Factories;

use App\Enum\DepreciationMethod;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'depreciation_method' => fake()->randomElement(array_column(DepreciationMethod::cases(), 'value')),
            'useful_life_years' => fake()->numberBetween(3, 20),
        ];
    }
}
