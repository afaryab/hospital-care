<?php

namespace App\Filament\Widgets;

use App\Console\Commands\fetchOld;
use App\CounterStatus;
use App\ExpenseVoucherStatus;
use App\Models\Closing;
use App\Models\Expense;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\Transaction;
use App\Models\UpgradeProcess;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    public function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $MigratedSteps = UpgradeProcess::where('name', 'currentStep')->first()->value ?? 0;
        $totalSteps = fetchOld::$TOTAL_STEPS;

        $percentageMigrated = $totalSteps > 0 ? round(($MigratedSteps / $totalSteps) * 100, 2) . '%' : '0%';

        $SyncPercentage = UpgradeProcess::where('name', 'percentage_synced')->first()->value ?? 0;

        $transactions = Transaction::count();
        $transactionVolume = Transaction::sum('amount');

        return [
            StatsOverviewWidget\Stat::make(
                label: 'Proceedural Migration',
                value: $percentageMigrated,
            )
            ->description("{$MigratedSteps} of {$totalSteps} steps migrated"),
            StatsOverviewWidget\Stat::make(
                label: 'Sync Percentage',
                value: "{$SyncPercentage} %",
            )
            ->description("{$transactions} Transactions worth {$this->moneyfy($transactionVolume)} are synced"),
            $this->getUserStats($startDate, $endDate),
            $this->getServiceStats($startDate, $endDate),
            $this->getPatientStats($startDate, $endDate),
            $this->getCounterStats($startDate, $endDate),
            $this->getExpenseVoucherStats($startDate, $endDate),
            $this->getExpenseStats($startDate, $endDate),
            $this->getTransactionStats($startDate, $endDate),
        ];
    }

    public function getUserStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalUsers = User::count();

        $userThisDuration = User::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        $userChartThisDuration = User::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
                label: 'New Users',
                value: "{$this->moneyfy($userThisDuration)}",
            )
            ->description("Total: {$this->moneyfy($totalUsers)}")
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
                value: "{$this->moneyfy($serviceThisDuration)}",
            )
            ->description("Total: {$this->moneyfy($totalServices)} in {$this->moneyfy($totalServiceDepartments)} Departments")
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
                value: "{$this->moneyfy($patientThisDuration)}",
            )
            ->description("Total: {$this->moneyfy($totalPatients)}")
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
                value: "{$this->moneyfy($totalCollectionThisDuration)} / {$this->moneyfy($totalOpeningsThisDuration)} / {$this->moneyfy($totalClosingsThisDuration)} ",
            )
            ->description("Total: {$this->moneyfy($totalCollection)} / {$this->moneyfy($totalOpenings)} / {$this->moneyfy($totalClosings)} / {$receptions}")
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
                value: "{$this->moneyfy($totalThisDuration)} ({$this->moneyfy($expenseThisDuration)}) ",
            )
            ->description("Total: {$this->moneyfy($totalExpenses)} ({$this->moneyfy($total)})")
            ->chart($expenseChartThisDuration->pluck('count')->toArray());
    }

    public function getExpenseStats($startDate = null, $endDate = null): StatsOverviewWidget\Stat
    {

        $totalExpenses = Expense::sum('amount');

        $expenseThisDuration = Expense::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $expenseChartThisDuration = Expense::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return StatsOverviewWidget\Stat::make(
                label: 'Expenses',
                value: "{$this->moneyfy($expenseThisDuration)}",
            )
            ->description("Total: {$this->moneyfy($totalExpenses)}")
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
                value: "{$this->moneyfy($totalCollectionThisDuration)} / {$this->moneyfy($totalThisDuration)}",
            )
            ->description("Total: {$this->moneyfy($totalCollection)} / {$this->moneyfy($total)}")
            ->chart($totalChartThisDuration->pluck('count')->toArray());
    }



    /**
     * Moneyfy number, take number and convert it to K, M, B and trillion format
     */

    private function moneyfy($number): string
    {
        if ($number >= 1_000_000_000_000) {
            return round($number / 1_000_000_000_000, 2) . 'T';
        } elseif ($number >= 1_000_000_000) {
            return round($number / 1_000_000_000, 2) . 'B';
        } elseif ($number >= 1_000_000) {
            return round($number / 1_000_000, 2) . 'M';
        } elseif ($number >= 1_000) {
            return round($number / 1_000, 2) . 'K';
        } else {
            return (string)$number;
        }
    }
}
