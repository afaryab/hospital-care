<?php

namespace Database\Factories;

use App\Models\Closing;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $amount = fake()->randomFloat(2, 100, 10000);
        $now = now();

        return [
            'tr_number' => sprintf('TR/%s/%s/%s/%04d', $now->format('Y'), $now->format('m'), $now->format('d'), $sequence),
            'closing_id' => Closing::factory(),
            'created_by' => User::factory(),
            'patient_id' => Patient::factory(),
            'income_or_expense' => 'INCOME',
            'type' => 'CASH',
            'amount' => $amount,
            'orignal_amount' => $amount,
            'amount_alphabetical' => 'Amount',
        ];
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'income_or_expense' => 'EXPENSE',
        ]);
    }
}
