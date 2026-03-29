<?php

namespace Database\Factories;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministratorFactory extends Factory
{
    protected $model = Administrator::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authority' => fake()->randomElement(['assistant', 'administrator', 'superadmin']),
        ];
    }
}
