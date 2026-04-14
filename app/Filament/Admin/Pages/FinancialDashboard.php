<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\Financial\CounterCollectionChart;
use App\Filament\Admin\Widgets\Financial\ExpenseCategoryBreakdown;
use App\Filament\Admin\Widgets\Financial\ExpenseVoucherStatus;
use App\Filament\Admin\Widgets\Financial\FinancialKPIStats;
use App\Filament\Admin\Widgets\Financial\IncomeVsExpenseTrend;
use App\Filament\Admin\Widgets\Financial\MonthlyRevenueTrend;
use App\Filament\Admin\Widgets\Financial\PanelIncomeBreakdown;
use App\Filament\Admin\Widgets\Financial\ReceivablesAging;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class FinancialDashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'financial-dashboard';

    protected static ?string $slug = 'financial-dashboard';

    protected static ?int $navigationSort = -14;

    protected static ?string $title = 'Financial Analytics';

    protected static ?string $navigationLabel = 'Financial Analytics';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            FinancialKPIStats::class,
            MonthlyRevenueTrend::class,
            IncomeVsExpenseTrend::class,
            ExpenseCategoryBreakdown::class,
            ReceivablesAging::class,
            PanelIncomeBreakdown::class,
            CounterCollectionChart::class,
            ExpenseVoucherStatus::class,
        ];
    }
}
