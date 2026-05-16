<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Concerns\HasDashboardDateFilters;
use App\Filament\Admin\Widgets\Patient\AgeGroupDistributionChart;
use App\Filament\Admin\Widgets\Patient\GenderDistributionChart;
use App\Filament\Admin\Widgets\Patient\NewVsReturningChart;
use App\Filament\Admin\Widgets\Patient\OutstandingReceivablesStats;
use App\Filament\Admin\Widgets\Patient\PatientDemographicsStats;
use App\Filament\Admin\Widgets\Patient\PatientsByDepartmentChart;
use App\Filament\Admin\Widgets\Patient\RegistrationTrendChart;
use App\Filament\Admin\Widgets\Patient\ReturningPatientsStats;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class PatientAnalytics extends BaseDashboard
{
    use HasDashboardDateFilters;

    protected static string $routePath = 'patient-analytics';

    protected static ?string $slug = 'patient-analytics';

    protected static ?int $navigationSort = -13;

    protected static ?string $title = 'Patient Analytics';

    protected static ?string $navigationLabel = 'Patient Analytics';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static \UnitEnum|string|null $navigationGroup = 'Dashboards';

    public function getWidgets(): array
    {
        return [
            PatientDemographicsStats::class,
            ReturningPatientsStats::class,
            NewVsReturningChart::class,
            RegistrationTrendChart::class,
            GenderDistributionChart::class,
            PatientsByDepartmentChart::class,
            AgeGroupDistributionChart::class,
            OutstandingReceivablesStats::class,
        ];
    }
}
