<?php

namespace Database\Factories;

use App\Models\HospitalSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class HospitalSettingFactory extends Factory
{
    protected $model = HospitalSetting::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
        ];
    }
}
