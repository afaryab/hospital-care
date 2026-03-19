<?php

namespace Database\Factories;

use App\Models\Closing;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'tr_number' => 'TR/' . $now->format('Y') . '/' . $now->format('m') . '/' . $now->format('d') . '/' . fake()->unique()->numerify('####'),
            'closing_id' => Closing::factory(),
            'patient_id' => Patient::factory(),
            'created_by' => User::factory(),
            'type' => 'OPD',
            'income_or_expense' => 'INCOME',
            'amount' => fake()->randomFloat(2, 100, 10000),
            'orignal_amount' => 0,
            'customer_payed' => 0,
            'change' => 0,
        ];
    }

    public function expense(): static
    {
        return $this->state(fn () => ['income_or_expense' => 'EXPENSE']);
    }
}
