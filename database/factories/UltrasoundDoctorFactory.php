<?php

namespace Database\Factories;

use App\Models\UltrasoundDoctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UltrasoundDoctorFactory extends Factory
{
    protected $model = UltrasoundDoctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
