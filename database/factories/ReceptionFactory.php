<?php

namespace Database\Factories;

use App\Models\Reception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceptionFactory extends Factory
{
    protected $model = Reception::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Reception',
            'is_allowed_to_pay_voucher' => false,
            'is_allowed_to_pay_from_petty_cash' => false,
        ];
    }
}
