<?php

namespace Database\Factories;

use App\Models\Accountant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountantFactory extends Factory
{
    protected $model = Accountant::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
