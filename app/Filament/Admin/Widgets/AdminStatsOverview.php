<?php

namespace App\Filament\Admin\Widgets;

use App\Enum\CounterStatus;
use App\Helpers\DateHelper;
use App\Helpers\NumberHelper;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class AdminStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    public function getStats(): array
    {
        // Interpret the picked range in the hospital timezone, then resolve the
        // exact UTC instants so datetime-column filters line up with stored data.
        $startDate = DateHelper::dayStartUtc($this->pageFilters['startDate'] ?? Carbon::now(DateHelper::timezone())->startOfMonth());
        $endDate = DateHelper::dayEndUtc($this->pageFilters['endDate'] ?? Carbon::now(DateHelper::timezone()));

        $totals = $this->allTimeTotals();

        return [
            $this->getUserStats($totals, $startDate, $endDate),
            $this->getServiceStats($totals, $startDate, $endDate),
            $this->getPatientStats($totals, $startDate, $endDate),
            $this->getCounterStats($totals, $startDate, $endDate),
            $this->getExpenseVoucherStats($totals, $startDate, $endDate),
            $this->getTransactionStats($totals, $startDate, $endDate),
        ];
    }

    /**
     * All-time (not date-range-scoped) aggregates for this widget — these
     * were previously run fresh on every dashboard load, several as
     * unscoped full-table SUM/COUNT queries. This is the first widget
     * rendered on the default admin dashboard, so that cost was paid on
     * every single page load. Cached for 1 hour, same pattern (and same
     * reasoning) as HistoryOverallStats::getStats(). Only the date-range-
     * scoped "this duration" queries below stay live, since caching those
     * under a fixed key would show stale figures when the date filter changes.
     *
     * @return array<string, mixed>
     */
    protected function allTimeTotals(): array
    {
        return Cache::remember('dashboard.admin.alltime_totals', 3600, function () {
            $closingTotals = Closing::selectRaw('SUM(closing_amount) - SUM(opening_amount) as net, COUNT(*) as total_closings')->first();

            return [
                'total_users' => User::query()->nonSystem()->count(),
                'total_service_departments' => ServiceDepartment::count(),
                'total_services' => Service::count(),
                'total_patients' => Patient::count(),
                'closing_net' => $closingTotals->net ?? 0,
                'closing_count' => $closingTotals->total_closings ?? 0,
                'total_openings' => Closing::where('status', CounterStatus::OPEN)->count(),
                'receptions' => Reception::count(),
                'expense_voucher_amount' => ExpenseVoucher::sum('amount'),
                'expense_voucher_count' => ExpenseVoucher::count(),
                'transaction_amount' => Transaction::sum('amount'),
                'transaction_count' => Transaction::count(),
            ];
        });
    }

    public function getUserStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $allowedUsers = env('MAX_USERS_ALLOWED', 10);

        $userThisDuration = User::query()
            ->nonSystem()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->count();

        $userChartThisDuration = User::query()
            ->nonSystem()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Users',
            value: NumberHelper::moneyfy($userThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['total_users']).' / '.NumberHelper::moneyfy($allowedUsers))
            ->chart($userChartThisDuration->pluck('count')->toArray());
    }

    public function getServiceStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $serviceThisDuration = Service::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->count();

        $serviceChartThisDuration = Service::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Services',
            value: NumberHelper::moneyfy($serviceThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['total_services']).' in '.NumberHelper::moneyfy($totals['total_service_departments']).' Departments')
            ->chart($serviceChartThisDuration->pluck('count')->toArray());
    }

    public function getPatientStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $patientThisDuration = Patient::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->count();

        $patientChartThisDuration = Patient::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Patients',
            value: NumberHelper::moneyfy($patientThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['total_patients']))
            ->chart($patientChartThisDuration->pluck('count')->toArray());
    }

    public function getCounterStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $periodTotals = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('SUM(closing_amount) - SUM(opening_amount) as net, COUNT(*) as total_closings')
            ->first();
        $totalCollectionThisDuration = $periodTotals->net ?? 0;
        $totalClosingsThisDuration = $periodTotals->total_closings ?? 0;
        $totalOpeningsThisDuration = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->where('status', CounterStatus::OPEN)
            ->count();

        $totalChartThisDuration = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(closing_amount) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Closings Worth / Open / Total',
            value: NumberHelper::moneyfy($totalCollectionThisDuration).' / '.NumberHelper::moneyfy($totalOpeningsThisDuration).' / '.NumberHelper::moneyfy($totalClosingsThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['closing_net']).' / '.NumberHelper::moneyfy($totals['total_openings']).' / '.NumberHelper::moneyfy($totals['closing_count']).' / '.$totals['receptions'])
            ->chart($totalChartThisDuration->pluck('count')->toArray());
    }

    public function getExpenseVoucherStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $expenseThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->sum('amount');

        $totalThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->count();

        $expenseChartThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Exp-Vouchers Issued (Worth)',
            value: NumberHelper::moneyfy($totalThisDuration).' ('.NumberHelper::moneyfy($expenseThisDuration).')',
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['expense_voucher_amount']).' ('.NumberHelper::moneyfy($totals['expense_voucher_count']).')')
            ->chart($expenseChartThisDuration->pluck('count')->toArray());
    }

    public function getTransactionStats(array $totals, $startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {
        $totalCollectionThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->sum('amount');
        $totalThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->count();

        $totalChartThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->where('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Transactions Worth / Total',
            value: NumberHelper::moneyfy($totalCollectionThisDuration).' / '.NumberHelper::moneyfy($totalThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totals['transaction_amount']).' / '.NumberHelper::moneyfy($totals['transaction_count']))
            ->chart($totalChartThisDuration->pluck('count')->toArray());
    }
}
