<?php

namespace Database\Factories;

use App\Models\Consent;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentFactory extends Factory
{
    protected $model = Consent::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'service_order_id' => null,
            'consent_type' => fake()->randomElement(['treatment', 'procedure', 'data_sharing']),
            'consent_method' => fake()->randomElement(['digital_checkbox', 'paper_signed', 'verbal_recorded']),
            'consented_at' => now(),
            'recorded_by' => User::factory(),
            'notes' => null,
        ];
    }
}
