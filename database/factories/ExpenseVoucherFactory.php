<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseVoucher>
 */
class ExpenseVoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exp_category_id' => ExpenseCategory::factory(),
            'payed_to_name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'notes' => fake()->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'transaction_id' => 1,
            'transaction_element_id' => 1,
        ]);
    }
}
