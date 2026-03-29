<?php

namespace Database\Factories;

use App\Models\Dentist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DentistFactory extends Factory
{
    protected $model = Dentist::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
