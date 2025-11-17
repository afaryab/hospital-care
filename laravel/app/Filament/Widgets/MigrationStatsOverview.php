<?php

namespace App\Filament\Widgets;

use App\Console\Commands\fetchOld;
use App\Enum\CounterStatus;
use App\Enum\ExpenseVoucherStatus;
use App\Helpers\NumberHelper;
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

class MigrationStatsOverview extends StatsOverviewWidget
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
            ->description("{$transactions} Transactions worth " . NumberHelper::moneyfy($transactionVolume) . " are synced"),
            
        ];
    }
}
