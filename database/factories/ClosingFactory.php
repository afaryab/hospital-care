<?php

namespace Database\Factories;

use App\Models\Closing;
use App\Models\Reception;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClosingFactory extends Factory
{
    protected $model = Closing::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;

        $now = now();

        return [
            'ct_number' => sprintf('CT/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'reception_id' => Reception::factory(),
            'receptionist_id' => User::factory(),
            'status' => 'OPEN',
            'opening_amount' => 0,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'CLOSED',
            'closed_at' => now(),
        ]);
    }
}
