<?php

namespace App\Filament\Admin\Widgets\Operations;

use App\Enum\CounterStatus;
use App\Helpers\NumberHelper;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Reception;
use App\Models\ServiceOrder;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class OperationsKPIStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $totalServiceOrders = ServiceOrder::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->count();

        $openCounters = Closing::query()
            ->where('status', CounterStatus::OPEN)
            ->count();

        $totalCounters = Reception::count();

        $totalClosings = Closing::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->count();

        $pendingVouchersCount = ExpenseVoucher::query()
            ->whereNull('transaction_id')
            ->count();

        $pendingVouchersAmount = ExpenseVoucher::query()
            ->whereNull('transaction_id')
            ->sum('amount');

        $serviceOrdersByType = ServiceOrder::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Total Service Orders', NumberHelper::moneyfy($totalServiceOrders))
                ->description('Orders in selected period')
                ->color('primary')
                ->chart($serviceOrdersByType),

            Stat::make('Open Counters Now', $openCounters.' / '.$totalCounters)
                ->description('Active counters / Total receptions')
                ->color($openCounters > 0 ? 'success' : 'gray'),

            Stat::make('Counter Sessions', NumberHelper::moneyfy($totalClosings))
                ->description('Closing sessions in period')
                ->color('info'),

            Stat::make('Pending Expense Vouchers', NumberHelper::moneyfy($pendingVouchersCount))
                ->description('Worth: PKR '.NumberHelper::moneyfy($pendingVouchersAmount))
                ->color('warning'),
        ];
    }
}
