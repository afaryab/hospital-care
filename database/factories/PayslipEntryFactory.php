<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use App\Models\PayslipEntry;
use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayslipEntryFactory extends Factory
{
    protected $model = PayslipEntry::class;

    public function definition(): array
    {
        $gross = fake()->numberBetween(25000, 150000);
        $deductionAmount = round($gross * 0.05, 2);

        return [
            'payroll_period_id' => PayrollPeriod::factory(),
            'user_id' => User::factory(),
            'salary_structure_id' => SalaryStructure::factory(),
            'gross_salary' => $gross,
            'deductions' => [
                ['label' => 'Income Tax', 'amount' => $deductionAmount],
            ],
            'total_deductions' => $deductionAmount,
            'net_salary' => $gross - $deductionAmount,
            'payment_method' => 'bank_transfer',
            'paid_at' => null,
            'paid_via_voucher_id' => null,
        ];
    }
}
