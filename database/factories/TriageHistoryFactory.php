<?php

namespace Database\Factories;

use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\TriageHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TriageHistory>
 */
class TriageHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'treatment_record_id' => TreatmentRecord::factory(),
            'service_order_id' => ServiceOrder::factory(),
            'old_triage_id' => null,
            'new_triage_id' => Triage::factory(),
            'changed_by' => User::factory(),
            'changed_at' => now(),
        ];
    }
}
