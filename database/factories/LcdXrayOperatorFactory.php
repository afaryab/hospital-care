<?php

namespace Database\Factories;

use App\Models\LcdXrayOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdXrayOperatorFactory extends Factory
{
    protected $model = LcdXrayOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
