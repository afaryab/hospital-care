<?php

namespace Database\Factories;

use App\Models\NursingStaff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NursingStaffFactory extends Factory
{
    protected $model = NursingStaff::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'manager']),
        ];
    }
}
