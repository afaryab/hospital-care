<?php

namespace App\Filament\Admin\Widgets;

use App\Enum\CounterStatus;
use App\Helpers\NumberHelper;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;

class AdminStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    public function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return [
            $this->getUserStats($startDate, $endDate),
            $this->getServiceStats($startDate, $endDate),
            $this->getPatientStats($startDate, $endDate),
            $this->getCounterStats($startDate, $endDate),
            $this->getExpenseVoucherStats($startDate, $endDate),
            $this->getTransactionStats($startDate, $endDate),
        ];
    }

    public function getUserStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalUsers = User::query()->nonSystem()->count();

        $allowedUsers = env('MAX_USERS_ALLOWED', 10);
        $isUserLimitReached = $totalUsers >= $allowedUsers;

        $userThisDuration = User::query()
            ->nonSystem()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $userChartThisDuration = User::query()
            ->nonSystem()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Users',
            value: NumberHelper::moneyfy($userThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totalUsers).' / '.NumberHelper::moneyfy($allowedUsers))
            ->chart($userChartThisDuration->pluck('count')->toArray());
    }

    public function getServiceStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalServiceDepartments = ServiceDepartment::count();
        $totalServices = Service::count();

        $serviceThisDuration = Service::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $serviceChartThisDuration = Service::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Services',
            value: NumberHelper::moneyfy($serviceThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totalServices).' in '.NumberHelper::moneyfy($totalServiceDepartments).' Departments')
            ->chart($serviceChartThisDuration->pluck('count')->toArray());
    }

    public function getPatientStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalPatients = Patient::count();

        $patientThisDuration = Patient::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $patientChartThisDuration = Patient::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'New Patients',
            value: NumberHelper::moneyfy($patientThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totalPatients))
            ->chart($patientChartThisDuration->pluck('count')->toArray());
    }

    public function getCounterStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalCollection = Closing::sum('closing_amount') - Closing::sum('opening_amount');
        $totalClosings = Closing::count();
        $totalOpenings = Closing::where('status', CounterStatus::OPEN)->count();
        $receptions = Reception::count();

        $totalCollectionThisDuration = (
            Closing::query()
                ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('closing_amount')
        ) - (
            Closing::query()
                ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->sum('opening_amount')
        );
        $totalClosingsThisDuration = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();
        $totalOpeningsThisDuration = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->where('status', CounterStatus::OPEN)
            ->count();

        $totalChartThisDuration = Closing::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(closing_amount) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Closings Worth / Open / Total',
            value: NumberHelper::moneyfy($totalCollectionThisDuration).' / '.NumberHelper::moneyfy($totalOpeningsThisDuration).' / '.NumberHelper::moneyfy($totalClosingsThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totalCollection).' / '.NumberHelper::moneyfy($totalOpenings).' / '.NumberHelper::moneyfy($totalClosings).' / '.$receptions)
            ->chart($totalChartThisDuration->pluck('count')->toArray());
    }

    public function getExpenseVoucherStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalExpenses = ExpenseVoucher::sum('amount');
        $total = ExpenseVoucher::count();

        $expenseThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $totalThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $expenseChartThisDuration = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Exp-Vouchers Issued (Worth)',
            value: NumberHelper::moneyfy($totalThisDuration).' ('.NumberHelper::moneyfy($expenseThisDuration).')',
        )
            ->description('Total: '.NumberHelper::moneyfy($totalExpenses).' ('.NumberHelper::moneyfy($total).')')
            ->chart($expenseChartThisDuration->pluck('count')->toArray());
    }

    public function getTransactionStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalCollection = Transaction::sum('amount');
        $total = Transaction::count();

        $totalCollectionThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('amount');
        $totalThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $totalChartThisDuration = Transaction::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
            label: 'Transactions Worth / Total',
            value: NumberHelper::moneyfy($totalCollectionThisDuration).' / '.NumberHelper::moneyfy($totalThisDuration),
        )
            ->description('Total: '.NumberHelper::moneyfy($totalCollection).' / '.NumberHelper::moneyfy($total))
            ->chart($totalChartThisDuration->pluck('count')->toArray());
    }
}
