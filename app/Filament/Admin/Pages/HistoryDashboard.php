<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\History\ExpensesByYearChart;
use App\Filament\Admin\Widgets\History\HistoryOverallStats;
use App\Filament\Admin\Widgets\History\PatientsByYearChart;
use App\Filament\Admin\Widgets\History\RevenueByYearChart;
use App\Filament\Admin\Widgets\History\ServiceOrdersByYearChart;
use App\Filament\Admin\Widgets\History\TopDepartmentsAllTime;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class HistoryDashboard extends BaseDashboard
{
    protected static string $routePath = 'history-dashboard';

    protected static ?string $slug = 'history-dashboard';

    protected static ?int $navigationSort = -11;

    protected static ?string $title = 'History & All-Time Stats';

    protected static ?string $navigationLabel = 'History';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getColumns(): int|array
    {
        return [
            'sm' => 4,
            'md' => 4,
            'xl' => 6,
        ];
    }

    public function getWidgets(): array
    {
        return [
            HistoryOverallStats::class,
            RevenueByYearChart::class,
            PatientsByYearChart::class,
            ServiceOrdersByYearChart::class,
            ExpensesByYearChart::class,
            TopDepartmentsAllTime::class,
        ];
    }
}
