<?php

namespace Database\Factories;

use App\Models\ServiceDepartment;
use App\Models\ServiceRecestation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceRecestationFactory extends Factory
{
    protected $model = ServiceRecestation::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name, '_'),
            'service_department_id' => ServiceDepartment::factory(),
            'charges' => fake()->randomFloat(2, 100, 5000),
            'charges_include_tax' => true,
            'tax_rate' => 0,
            'have_service_provider' => false,
            'created_by' => User::factory(),
        ];
    }
}
