<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'ps_number' => sprintf('PS/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['m', 'f']),
            'contact' => fake()->numerify('03#########'),
            'age_days' => fake()->numberBetween(365, 36500),
            'age_dob' => now()->subDays(fake()->numberBetween(365, 36500)),
        ];
    }

    public function withPsNumber(string $psNumber): static
    {
        return $this->state(fn (array $attributes) => [
            'ps_number' => $psNumber,
        ]);
    }
}
