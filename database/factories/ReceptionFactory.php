<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reception>
 */
class ReceptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Reception',
            'allowed_departments' => json_encode(['OPD']),
            'is_allowed_to_pay_voucher' => false,
            'is_allowed_to_pay_from_petty_cash' => false,
            'is_cash_allowed' => true,
            'is_cheques_allowed' => false,
            'is_card_allowed' => false,
        ];
    }
}
