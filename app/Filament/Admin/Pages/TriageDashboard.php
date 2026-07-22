<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\Triage\TriageDistributionChart;
use App\Filament\Admin\Widgets\Triage\TriageKPIStats;
use App\Filament\Admin\Widgets\Triage\TriageTrendChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class TriageDashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'triage-dashboard';

    protected static ?string $slug = 'triage-dashboard';

    protected static ?int $navigationSort = -9;

    protected static ?string $title = 'Triage Analytics';

    protected static ?string $navigationLabel = 'Triage Analytics';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            TriageKPIStats::class,
            TriageDistributionChart::class,
            TriageTrendChart::class,
        ];
    }
}
