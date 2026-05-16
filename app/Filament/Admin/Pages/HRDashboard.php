<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\HR\HRKPIStats;
use App\Filament\Admin\Widgets\HR\PayrollTrendChart;
use App\Filament\Admin\Widgets\HR\StaffActivityChart;
use App\Filament\Admin\Widgets\HR\StaffByRoleChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class HRDashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'hr-dashboard';

    protected static ?string $slug = 'hr-dashboard';

    protected static ?int $navigationSort = -9;

    protected static ?string $title = 'Human Resources';

    protected static ?string $navigationLabel = 'Human Resources';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            HRKPIStats::class,
            StaffByRoleChart::class,
            PayrollTrendChart::class,
            StaffActivityChart::class,
        ];
    }
}
