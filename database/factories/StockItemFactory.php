<?php

namespace Database\Factories;

use App\Models\StockCategory;
use App\Models\StockItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockItemFactory extends Factory
{
    protected $model = StockItem::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'sku' => fake()->unique()->bothify('SKU-####-??'),
            'category_id' => StockCategory::factory(),
            'unit' => fake()->randomElement(['pcs', 'ml', 'mg', 'box', 'strip', 'bottle', 'vial']),
            'reorder_level' => fake()->numberBetween(5, 50),
            'default_vendor' => fake()->company(),
            'is_active' => true,
        ];
    }
}
