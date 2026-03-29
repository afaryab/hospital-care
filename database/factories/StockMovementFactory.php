<?php

namespace Database\Factories;

use App\Enum\StockMovementType;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'stock_item_id' => StockItem::factory(),
            'type' => StockMovementType::In->value,
            'quantity' => fake()->randomFloat(2, 1, 100),
            'unit_cost' => fake()->randomFloat(2, 10, 1000),
            'moved_by' => User::factory(),
        ];
    }

    public function out(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => StockMovementType::Out->value,
        ]);
    }
}
