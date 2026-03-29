<?php

use App\Enum\PayrollPeriodStatus;
use App\Enum\SalaryAdvanceStatus;
use App\Models\PayrollPeriod;
use App\Models\PayslipEntry;
use App\Models\SalaryAdvance;
use App\Models\SalaryStructure;
use App\Models\User;

test('payroll period generates period number on create', function () {
    $period = PayrollPeriod::factory()->create();

    expect($period->period_number)->toStartWith('PAY/');
});

test('payroll period status casts to enum', function () {
    $period = PayrollPeriod::factory()->approved()->create();

    expect($period->status->value)->toBe(PayrollPeriodStatus::Approved->value);
});

test('payroll period has payslip entries relationship', function () {
    $period = PayrollPeriod::factory()->create();
    PayslipEntry::factory()->create(['payroll_period_id' => $period->id]);

    expect($period->payslipEntries()->count())->toBe(1);
});

test('salary structure belongs to user', function () {
    $user = User::factory()->create();
    $structure = SalaryStructure::factory()->create(['user_id' => $user->id]);

    expect($structure->user->id)->toBe($user->id);
});

test('payslip entry net salary equals gross minus total deductions', function () {
    $gross = 50000;
    $deductions = 2500;
    $entry = PayslipEntry::factory()->create([
        'gross_salary' => $gross,
        'total_deductions' => $deductions,
        'net_salary' => $gross - $deductions,
    ]);

    expect((float) $entry->net_salary)->toBe((float) ($gross - $deductions));
});

test('salary advance status casts correctly', function () {
    $advance = SalaryAdvance::factory()->fullyRecovered()->create();

    expect($advance->status->value)->toBe(SalaryAdvanceStatus::FullyRecovered->value)
        ->and((float) $advance->remaining_balance)->toBe(0.0);
});

test('salary advance belongs to user and granted by user', function () {
    $user = User::factory()->create();
    $granter = User::factory()->create();
    $advance = SalaryAdvance::factory()->create([
        'user_id' => $user->id,
        'granted_by' => $granter->id,
    ]);

    expect($advance->user->id)->toBe($user->id)
        ->and($advance->grantedBy->id)->toBe($granter->id);
});
