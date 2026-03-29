<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetAssignmentLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetAssignmentLogFactory extends Factory
{
    protected $model = AssetAssignmentLog::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'transferred_by' => User::factory(),
            'transferred_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
