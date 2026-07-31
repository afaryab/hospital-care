<?php

namespace Database\Factories;

use App\Models\LcdEmergencyOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdEmergencyOperatorFactory extends Factory
{
    protected $model = LcdEmergencyOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
