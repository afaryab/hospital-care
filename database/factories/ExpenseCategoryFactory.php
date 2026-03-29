<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['OPD', 'LAB', 'OTHER']),
            'pay_doc' => false,
            'pay_others' => false,
            'pay_users' => false,
            'pay_patient' => false,
            'allow_petty_cash' => true,
            'allow_voucher' => true,
        ];
    }
}
