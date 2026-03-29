<?php

namespace Database\Factories;

use App\Enum\PayrollPeriodStatus;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollPeriodFactory extends Factory
{
    protected $model = PayrollPeriod::class;

    public function definition(): array
    {
        $now = now();

        return [
            'period_number' => sprintf('PAY/%s/%s', $now->format('Y'), $now->format('m')),
            'year' => (int) $now->format('Y'),
            'month' => (int) $now->format('m'),
            'status' => PayrollPeriodStatus::Draft->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayrollPeriodStatus::Approved->value,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayrollPeriodStatus::Paid->value,
        ]);
    }
}
