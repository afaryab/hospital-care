<?php

namespace App\Filament\Admin\Widgets;

use App\Console\Commands\FetchOldHIMS;
use App\Helpers\NumberHelper;
use App\Models\Transaction;
use App\Models\UpgradeProcess;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;

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
        $totalSteps = FetchOldHIMS::$TOTAL_STEPS;

        $percentageMigrated = $totalSteps > 0 ? round(($MigratedSteps / $totalSteps) * 100, 2).'%' : '0%';

        $SyncPercentage = UpgradeProcess::where('name', 'transaction_migration_percentage')->first()->value ?? 0;

        $transactions = Transaction::count();
        $transactionVolume = Transaction::sum('amount');

        $TotalOldTransctions = UpgradeProcess::where('name', 'total_old_transactions')->first()->value ?? 0;

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
                ->description("{$transactions}/{$TotalOldTransctions} Transactions worth ".NumberHelper::moneyfy($transactionVolume).' are synced'),

        ];
    }
}
