<?php

namespace Database\Factories;

use App\Enum\ConsentMethod;
use App\Enum\ConsentType;
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
            'consent_type' => fake()->randomElement(ConsentType::cases()),
            'consent_method' => fake()->randomElement(ConsentMethod::cases()),
            'consented_at' => now(),
            'recorded_by' => User::factory(),
            'notes' => null,
        ];
    }

    public function treatment(): static
    {
        return $this->state(fn (array $attributes) => ['consent_type' => ConsentType::Treatment]);
    }
}
