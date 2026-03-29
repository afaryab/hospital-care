<?php

namespace Database\Factories;

use App\Models\Closing;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionElementFactory extends Factory
{
    protected $model = TransactionElement::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 5000);

        return [
            'closing_id' => Closing::factory(),
            'transaction_id' => Transaction::factory(),
            'created_by' => User::factory(),
            'patient_id' => Patient::factory(),
            'type' => 'OPD',
            'income_or_expense' => 'INCOME',
            'amount' => $amount,
            'orignal_amount' => $amount,
        ];
    }
}
