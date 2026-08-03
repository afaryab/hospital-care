<?php

namespace Database\Factories;

use App\Enum\AppointmentPriorityMode;
use App\Enum\AppointmentStatus;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'service_id' => Service::factory(),
            'doctor_id' => null,
            'scheduled_at' => now()->addDay(),
            'priority_mode' => AppointmentPriorityMode::Standard,
            'status' => AppointmentStatus::Scheduled,
            'created_by' => User::factory(),
        ];
    }

    public function priority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_mode' => AppointmentPriorityMode::Priority,
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_mode' => AppointmentPriorityMode::Medium,
        ]);
    }

    public function scheduledToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => now(),
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::NoShow,
        ]);
    }
}
