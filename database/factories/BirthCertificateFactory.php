<?php

namespace Database\Factories;

use App\Models\BirthCertificate;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BirthCertificateFactory extends Factory
{
    protected $model = BirthCertificate::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'service_order_id' => ServiceOrder::factory(),
            'birth_certificate_number' => sprintf('BC/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'child_name' => fake()->firstName(),
            'date_of_birth' => $now->toDateString(),
            'time_of_birth' => $now->toTimeString(),
            'gender' => fake()->randomElement(['m', 'f']),
            'place_of_birth' => null,
            'weight_at_birth' => fake()->randomFloat(2, 2, 4),
            'mother_name' => fake()->name('female'),
            'mother_cnic' => null,
            'father_name' => null,
            'father_cnic' => null,
            'attending_doctor_id' => User::factory(),
            'remarks' => null,
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'created_by' => User::factory(),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => User::factory(),
        ]);
    }
}
