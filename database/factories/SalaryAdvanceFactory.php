<?php

namespace Database\Factories;

use App\Enum\SalaryAdvanceStatus;
use App\Models\SalaryAdvance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryAdvanceFactory extends Factory
{
    protected $model = SalaryAdvance::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(5000, 50000);
        $deductionPerMonth = round($amount / 3, 2);

        return [
            'user_id' => User::factory(),
            'amount' => $amount,
            'granted_by' => User::factory(),
            'granted_at' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'deduction_per_month' => $deductionPerMonth,
            'remaining_balance' => $amount,
            'status' => SalaryAdvanceStatus::Active->value,
            'notes' => null,
        ];
    }

    public function fullyRecovered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SalaryAdvanceStatus::FullyRecovered->value,
            'remaining_balance' => 0,
        ]);
    }
}
