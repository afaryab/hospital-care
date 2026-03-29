<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'service_department_id' => ServiceDepartment::factory(),
            'charges' => fake()->randomFloat(2, 100, 5000),
            'charges_include_tax' => false,
            'tax_rate' => 0,
            'have_service_provider' => false,
            'service_provider_types' => null,
            'is_composit_service' => false,
            'is_featured' => false,
            'generate_service_order' => true,
            'created_by' => User::factory(),
        ];
    }
}
