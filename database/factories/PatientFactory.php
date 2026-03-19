<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['m', 'f']),
            'contact' => fake()->numerify('03#########'),
            'address' => fake()->address(),
            'age_days' => fake()->numberBetween(365, 36500),
        ];
    }

    public function withPsNumber(string $psNumber): static
    {
        return $this->state(fn () => ['ps_number' => $psNumber]);
    }
}
