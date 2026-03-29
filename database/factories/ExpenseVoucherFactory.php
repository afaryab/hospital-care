<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseVoucherFactory extends Factory
{
    protected $model = ExpenseVoucher::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'vc_number' => sprintf('VC/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'exp_category_id' => ExpenseCategory::factory(),
            'payed_to' => User::factory(),
            'payed_to_name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
