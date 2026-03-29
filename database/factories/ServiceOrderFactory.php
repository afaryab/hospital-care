<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceOrderFactory extends Factory
{
    protected $model = ServiceOrder::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'type' => 'OPD',
            'so_number' => sprintf('PS/%s/%s/%04d/OPD/%02d', $now->format('Y'), $now->format('m'), $sequence, 1),
            'so_short' => sprintf('%08d', $sequence),
            'created_by' => User::factory(),
            'patient_id' => Patient::factory(),
            'service_id' => Service::factory(),
        ];
    }
}
