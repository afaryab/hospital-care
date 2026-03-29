<?php

namespace Database\Factories;

use App\Models\IndDoctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndDoctorFactory extends Factory
{
    protected $model = IndDoctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
