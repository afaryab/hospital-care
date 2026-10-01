<?php

namespace Database\Factories;

use App\Enum\AppointmentRequestStatus;
use App\Models\AppointmentRequest;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentRequest>
 */
class AppointmentRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'contact' => '03'.fake()->numerify('#########'),
            'gender' => fake()->randomElement(['m', 'f']),
            'age_years' => fake()->numberBetween(1, 80),
            'service_id' => Service::factory(),
            'preferred_date' => now()->addDays(2)->toDateString(),
            'preferred_time' => 'morning',
            'notes' => null,
            'status' => AppointmentRequestStatus::Pending,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => AppointmentRequestStatus::Confirmed]);
    }
}
