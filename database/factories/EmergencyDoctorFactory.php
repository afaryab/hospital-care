<?php

namespace Database\Factories;

use App\Models\EmergencyDoctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmergencyDoctorFactory extends Factory
{
    protected $model = EmergencyDoctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
