<?php

namespace Database\Factories;

use App\Models\TreatmentRecord;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Database\Eloquent\Factories\Factory;

class VitalSignFactory extends Factory
{
    protected $model = VitalSign::class;

    public function definition(): array
    {
        return [
            'treatment_record_id' => TreatmentRecord::factory(),
            'temperature' => fake()->randomFloat(1, 96, 104),
            'blood_pressure_systolic' => fake()->numberBetween(90, 180),
            'blood_pressure_diastolic' => fake()->numberBetween(60, 120),
            'pulse_rate' => fake()->numberBetween(50, 130),
            'respiratory_rate' => fake()->numberBetween(12, 30),
            'oxygen_saturation' => fake()->randomFloat(1, 90, 100),
            'weight' => fake()->randomFloat(1, 30, 150),
            'height' => fake()->randomFloat(1, 100, 210),
            'recorded_at' => now(),
            'recorded_by' => User::factory(),
        ];
    }
}
