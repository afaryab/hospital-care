<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Helpers\NumberHelper;
use App\Models\Patient;
use App\Models\Receaveable;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PatientDemographicsStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $totalPatients = Patient::count();

        $newPatients = Patient::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->count();

        $maleCount = Patient::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->where('gender', 'm')
            ->count();

        $femaleCount = Patient::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->where('gender', 'f')
            ->count();

        $patientsWithBalance = Receaveable::query()
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->distinct('patient_id')
            ->count('patient_id');

        $totalOutstanding = Receaveable::query()
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->sum('amount');

        $trend = Patient::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Total Patients', NumberHelper::moneyfy($totalPatients))
                ->description('All registered patients')
                ->color('primary')
                ->chart($trend),

            Stat::make('New This Period', NumberHelper::moneyfy($newPatients))
                ->description('Registered in selected range')
                ->color('success')
                ->chart($trend),

            Stat::make('Gender Split (M/F)', $maleCount.' / '.$femaleCount)
                ->description('Male / Female registrations in period')
                ->color('info'),

            Stat::make('Patients with Balance', NumberHelper::moneyfy($patientsWithBalance))
                ->description('Outstanding: PKR '.NumberHelper::moneyfy($totalOutstanding))
                ->color('warning'),
        ];
    }
}
