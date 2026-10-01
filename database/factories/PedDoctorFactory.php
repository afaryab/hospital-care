<?php

namespace Database\Factories;

use App\Models\PedDoctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedDoctorFactory extends Factory
{
    protected $model = PedDoctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
