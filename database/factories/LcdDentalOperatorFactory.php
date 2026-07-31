<?php

namespace Database\Factories;

use App\Models\LcdDentalOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdDentalOperatorFactory extends Factory
{
    protected $model = LcdDentalOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
