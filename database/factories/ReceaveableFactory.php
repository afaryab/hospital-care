<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceaveableFactory extends Factory
{
    protected $model = Receaveable::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 500, 10000);

        return [
            'patient_id' => Patient::factory(),
            'transaction_id' => Transaction::factory(),
            'amount' => $amount,
            'orignal_amount' => $amount,
            'status' => 'PENDING',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => 0,
            'status' => 'PAID',
        ]);
    }
}
