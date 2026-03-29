<?php

namespace Database\Factories;

use App\Models\Panel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PanelFactory extends Factory
{
    protected $model = Panel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->bothify('??##'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
