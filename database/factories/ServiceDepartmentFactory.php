<?php

namespace Database\Factories;

use App\Models\ServiceDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceDepartmentFactory extends Factory
{
    protected $model = ServiceDepartment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fake()->unique()->slug(2),
            'image' => 'default.png',
            'have_composit_services' => false,
        ];
    }
}
