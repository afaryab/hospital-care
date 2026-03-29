<?php

namespace Database\Factories;

use App\Models\OpdDoctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpdDoctorFactory extends Factory
{
    protected $model = OpdDoctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
