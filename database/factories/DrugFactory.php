<?php

namespace Database\Factories;

use App\Models\Drug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Drug>
 */
class DrugFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' '.fake()->randomElement(['500mg', '250mg', 'Syrup', 'Forte']),
            'generic_name' => fake()->word(),
            'type' => fake()->randomElement(Drug::types()),
            'drug_category_id' => null,
            'strength' => fake()->randomElement(['250mg', '500mg', '1g', '5mg/ml']),
            'manufacturer' => fake()->company(),
            'default_dose' => '1 tablet',
            'default_frequency' => fake()->randomElement(Drug::frequencies()),
            'default_duration' => fake()->randomElement(['3 days', '5 days', '7 days']),
            'default_route' => fake()->randomElement(Drug::routes()),
            'usage_instructions' => fake()->optional()->sentence(),
            'contraindications' => fake()->optional()->sentence(),
            'side_effects' => fake()->optional()->sentence(),
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
