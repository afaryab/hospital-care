<?php

namespace App\Filament\Admin\Widgets\HR;

use App\Models\PayrollPeriod;
use App\Models\PayslipEntry;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PayrollTrendChart extends ChartWidget
{
    protected ?string $heading = 'Payroll Cost by Period';

    protected ?string $description = 'Gross and net salary totals per processed payroll period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        // Last 12 payroll periods ordered chronologically
        $periods = PayrollPeriod::query()
            ->orderBy('year')
            ->orderBy('month')
            ->latest('id')
            ->limit(12)
            ->get(['id', 'year', 'month', 'status']);

        $periodIds = $periods->pluck('id');

        $payslipTotals = PayslipEntry::query()
            ->whereIn('payroll_period_id', $periodIds)
            ->select(
                'payroll_period_id',
                DB::raw('SUM(gross_salary) as gross'),
                DB::raw('SUM(net_salary) as net'),
                DB::raw('SUM(total_deductions) as deductions')
            )
            ->groupBy('payroll_period_id')
            ->get()
            ->keyBy('payroll_period_id');

        $labels = $periods->map(fn ($p) => date('M Y', mktime(0, 0, 0, $p->month, 1, $p->year)));

        return [
            'datasets' => [
                [
                    'label' => 'Gross Salary',
                    'data' => $periods->map(fn ($p) => $payslipTotals->get($p->id)?->gross ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Net Salary',
                    'data' => $periods->map(fn ($p) => $payslipTotals->get($p->id)?->net ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Deductions',
                    'data' => $periods->map(fn ($p) => $payslipTotals->get($p->id)?->deductions ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
