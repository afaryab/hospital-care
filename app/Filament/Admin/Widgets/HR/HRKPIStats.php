<?php

namespace App\Filament\Admin\Widgets\HR;

use App\Enum\PayrollPeriodStatus;
use App\Helpers\NumberHelper;
use App\Models\PayrollPeriod;
use App\Models\PayslipEntry;
use App\Models\SalaryStructure;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class HRKPIStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        // Total unique staff (any profile)
        $totalStaff = User::query()->nonSystem()->count();

        // New hires in period (users created in range with any staff profile)
        $newHires = User::query()
            ->nonSystem()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->count();

        // Total monthly salary burden from active salary structures
        $monthlySalaryBurden = SalaryStructure::query()
            ->whereNull('effective_to')
            ->orWhere('effective_to', '>=', now())
            ->selectRaw('SUM(basic_salary + housing_allowance + medical_allowance + transport_allowance) as total')
            ->value('total') ?? 0;

        // Pending payroll periods
        $pendingPayroll = PayrollPeriod::query()
            ->whereIn('status', [
                PayrollPeriodStatus::Draft->value,
                PayrollPeriodStatus::Calculated->value,
            ])
            ->count();

        // Net salary paid in period (from payslip entries)
        $salaryPaidInPeriod = PayslipEntry::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->sum('net_salary');

        // Trend: new hires per day
        $hireTrend = User::query()
            ->nonSystem()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt')
            ->toArray();

        return [
            Stat::make('Total Staff', NumberHelper::moneyfy($totalStaff))
                ->description('All registered users with roles')
                ->color('primary'),

            Stat::make('New Hires (Period)', NumberHelper::moneyfy($newHires))
                ->description('Staff registered in selected range')
                ->color($newHires > 0 ? 'success' : 'gray')
                ->chart($hireTrend),

            Stat::make('Monthly Salary Burden', 'PKR '.NumberHelper::moneyfy($monthlySalaryBurden))
                ->description('Active salary structures combined')
                ->color('warning'),

            Stat::make('Pending Payroll Periods', NumberHelper::moneyfy($pendingPayroll))
                ->description('Draft or calculated — awaiting approval')
                ->color($pendingPayroll > 0 ? 'danger' : 'success'),

            Stat::make('Net Salary Paid (Period)', 'PKR '.NumberHelper::moneyfy($salaryPaidInPeriod))
                ->description('From processed payslips in range')
                ->color('info'),
        ];
    }
}
