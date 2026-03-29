<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientManager;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientManagerFactory extends Factory
{
    protected $model = PatientManager::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_id' => Patient::factory(),
        ];
    }
}
