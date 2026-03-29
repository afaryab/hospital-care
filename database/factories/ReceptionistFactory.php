<?php

namespace Database\Factories;

use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceptionistFactory extends Factory
{
    protected $model = Receptionist::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
