<?php

namespace Database\Factories;

use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryStructureFactory extends Factory
{
    protected $model = SalaryStructure::class;

    public function definition(): array
    {
        $basic = fake()->numberBetween(20000, 100000);

        return [
            'user_id' => User::factory(),
            'basic_salary' => $basic,
            'housing_allowance' => round($basic * 0.2, 2),
            'medical_allowance' => round($basic * 0.1, 2),
            'transport_allowance' => round($basic * 0.05, 2),
            'other_allowances' => null,
            'effective_from' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'effective_to' => null,
        ];
    }
}
