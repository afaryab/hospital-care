<?php

namespace Database\Factories;

use App\Models\Triage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Triage>
 */
class TriageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'color' => fake()->randomElement(array_keys(Triage::colorOptions())),
            'priority' => fake()->numberBetween(1, 5),
            'description' => fake()->optional()->sentence(),
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
