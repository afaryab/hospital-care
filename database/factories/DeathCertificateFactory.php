<?php

namespace Database\Factories;

use App\Enum\DeathCertificateManner;
use App\Models\DeathCertificate;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeathCertificateFactory extends Factory
{
    protected $model = DeathCertificate::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'service_order_id' => ServiceOrder::factory(),
            'certificate_number' => sprintf('DC/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'date_of_death' => $now->toDateString(),
            'time_of_death' => $now->toTimeString(),
            'place_of_death' => null,
            'manner_of_death' => null,
            'antecedent_cause' => null,
            'informant_name' => null,
            'informant_relation' => null,
            'informant_cnic' => null,
            'remarks' => null,
            'created_by' => User::factory(),
        ];
    }

    public function withManner(DeathCertificateManner $manner): static
    {
        return $this->state(fn (array $attributes) => [
            'manner_of_death' => $manner,
        ]);
    }
}
