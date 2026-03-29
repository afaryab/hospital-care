<?php

namespace Database\Factories;

use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentRecordFactory extends Factory
{
    protected $model = TreatmentRecord::class;

    public function definition(): array
    {
        return [
            'service_order_id' => ServiceOrder::factory(),
            'department_id' => ServiceDepartment::factory(),
            'treating_doctor_id' => User::factory(),
            'chief_complaint' => fake()->sentence(),
            'history_of_present_illness' => fake()->paragraph(),
            'diagnosis_text' => fake()->sentence(),
            'treatment_plan' => fake()->paragraph(),
            'treated_at' => now(),
            'recorded_by' => User::factory(),
            'is_finalized' => false,
        ];
    }

    public function finalized(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_finalized' => true,
            'finalized_at' => now(),
        ]);
    }
}
