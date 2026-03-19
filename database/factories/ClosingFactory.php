<?php

namespace Database\Factories;

use App\Models\Reception;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Closing>
 */
class ClosingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = now();

        return [
            'ct_number' => 'CT/' . $now->format('Y') . '/' . $now->format('m') . '/' . fake()->unique()->numerify('####'),
            'reception_id' => Reception::factory(),
            'receptionist_id' => User::factory(),
            'status' => 'OPEN',
            'opening_amount' => fake()->randomFloat(2, 0, 10000),
            'closing_amount' => 0,
            'expense_payed' => 0,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'CLOSED',
            'closed_at' => now(),
        ]);
    }
}
