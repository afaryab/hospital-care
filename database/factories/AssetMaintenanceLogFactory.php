<?php

namespace Database\Factories;

use App\Enum\MaintenanceType;
use App\Models\Asset;
use App\Models\AssetMaintenanceLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetMaintenanceLogFactory extends Factory
{
    protected $model = AssetMaintenanceLog::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'type' => fake()->randomElement(array_column(MaintenanceType::cases(), 'value')),
            'description' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 0, 10000),
            'performed_by' => fake()->name(),
            'scheduled_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'completed_date' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'next_maintenance_date' => fake()->optional()->dateTimeBetween('+1 month', '+1 year'),
        ];
    }
}
