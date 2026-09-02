<?php

namespace Database\Factories;

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Enum\IncidentType;
use App\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(IncidentType::manuallyReportable()),
            'severity' => fake()->randomElement(IncidentSeverity::cases()),
            'status' => IncidentStatus::Reported,
            'occurred_at' => now(),
        ];
    }

    public function status(IncidentStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
