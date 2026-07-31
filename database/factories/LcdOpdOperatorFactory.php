<?php

namespace Database\Factories;

use App\Models\LcdOpdOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdOpdOperatorFactory extends Factory
{
    protected $model = LcdOpdOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
