<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\AdminStatsOverview;
use App\Filament\Admin\Widgets\Executive\DepartmentIncomeChart;
use App\Filament\Admin\Widgets\Executive\MonthlyRevenueComparison;
use App\Filament\Admin\Widgets\Executive\PatientRegistrationTrend;
use App\Filament\Admin\Widgets\Executive\PaymentMethodBreakdown;
use App\Filament\Admin\Widgets\Executive\RevenueVsExpenseChart;
use App\Filament\Admin\Widgets\Executive\TopServicesRevenue;
use App\Filament\Admin\Widgets\MigrationStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static ?int $navigationSort = -15;

    protected static ?string $title = 'Executive Overview';

    protected static ?string $navigationLabel = 'Executive Overview';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function getWidgets(): array
    {
        $widgets = [
            AdminStatsOverview::class,
            RevenueVsExpenseChart::class,
            PatientRegistrationTrend::class,
            MonthlyRevenueComparison::class,
            TopServicesRevenue::class,
            PaymentMethodBreakdown::class,
            DepartmentIncomeChart::class,
        ];

        if (env('ENABLE_OLD_SYNC', false) !== false) {
            $widgets[] = MigrationStatsOverview::class;
        }

        return $widgets;
    }
}
