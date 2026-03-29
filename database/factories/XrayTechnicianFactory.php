<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\XrayTechnician;
use Illuminate\Database\Eloquent\Factories\Factory;

class XrayTechnicianFactory extends Factory
{
    protected $model = XrayTechnician::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
