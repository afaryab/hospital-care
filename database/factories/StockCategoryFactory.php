<?php

namespace Database\Factories;

use App\Models\StockCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockCategoryFactory extends Factory
{
    protected $model = StockCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'parent_id' => null,
            'is_medicine' => false,
        ];
    }

    public function medicine(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_medicine' => true,
        ]);
    }

    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
