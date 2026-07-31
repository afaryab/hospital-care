<?php

namespace Database\Factories;

use App\Models\LcdIndOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdIndOperatorFactory extends Factory
{
    protected $model = LcdIndOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
