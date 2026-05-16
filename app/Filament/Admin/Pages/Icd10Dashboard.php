<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\Icd10\DepartmentDiagnosisChart;
use App\Filament\Admin\Widgets\Icd10\DiagnosesByCategoryChart;
use App\Filament\Admin\Widgets\Icd10\DiagnosisTrendChart;
use App\Filament\Admin\Widgets\Icd10\Icd10KPIStats;
use App\Filament\Admin\Widgets\Icd10\TopDiagnosesChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Icd10Dashboard extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'icd10-dashboard';

    protected static ?string $slug = 'icd10-dashboard';

    protected static ?int $navigationSort = -10;

    protected static ?string $title = 'ICD-10 Analytics';

    protected static ?string $navigationLabel = 'ICD-10 Analytics';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            Icd10KPIStats::class,
            TopDiagnosesChart::class,
            DiagnosesByCategoryChart::class,
            DiagnosisTrendChart::class,
            DepartmentDiagnosisChart::class,
        ];
    }
}
