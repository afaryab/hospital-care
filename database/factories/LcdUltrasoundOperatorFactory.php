<?php

namespace Database\Factories;

use App\Models\LcdUltrasoundOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdUltrasoundOperatorFactory extends Factory
{
    protected $model = LcdUltrasoundOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
