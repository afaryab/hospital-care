<?php

namespace Database\Factories;

use App\Models\ReferralCertificate;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralCertificateFactory extends Factory
{
    protected $model = ReferralCertificate::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'service_order_id' => ServiceOrder::factory(),
            'referral_number' => sprintf('RF/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'receiving_facility_name' => null,
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
