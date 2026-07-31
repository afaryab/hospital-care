<?php

namespace Database\Factories;

use App\Models\LcdLaboratoryOperator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LcdLaboratoryOperatorFactory extends Factory
{
    protected $model = LcdLaboratoryOperator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
