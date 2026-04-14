<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\Operations\CounterPerformanceChart;
use App\Filament\Admin\Widgets\Operations\ExpenseVoucherTrend;
use App\Filament\Admin\Widgets\Operations\OperationsKPIStats;
use App\Filament\Admin\Widgets\Operations\ReceptionistPerformanceChart;
use App\Filament\Admin\Widgets\Operations\ServiceOrdersByTypeChart;
use App\Filament\Admin\Widgets\Operations\TopServicesUtilization;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class OperationsDashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'operations-dashboard';

    protected static ?string $slug = 'operations-dashboard';

    protected static ?int $navigationSort = -12;

    protected static ?string $title = 'Operations Dashboard';

    protected static ?string $navigationLabel = 'Operations';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            OperationsKPIStats::class,
            ServiceOrdersByTypeChart::class,
            CounterPerformanceChart::class,
            TopServicesUtilization::class,
            ReceptionistPerformanceChart::class,
            ExpenseVoucherTrend::class,
        ];
    }
}
