<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Helpers\NumberHelper;
use App\Models\ServiceOrder;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ReturningPatientsStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $startDate = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->pageFilters['endDate'] ?? now());

        // Returning patients: had at least one service order in the period
        // AND were registered BEFORE the period started.
        $returning = ServiceOrder::query()
            ->whereBetween('service_orders.created_at', [$startDate, $endDate])
            ->whereNotNull('patient_id')
            ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
            ->where('patients.created_at', '<', $startDate)
            ->whereNull('patients.deleted_at')
            ->distinct('service_orders.patient_id')
            ->count('service_orders.patient_id');

        // New patients with visits: registered AND had a service order in the period.
        $newWithVisits = ServiceOrder::query()
            ->whereBetween('service_orders.created_at', [$startDate, $endDate])
            ->whereNotNull('patient_id')
            ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
            ->whereBetween('patients.created_at', [$startDate, $endDate])
            ->whereNull('patients.deleted_at')
            ->distinct('service_orders.patient_id')
            ->count('service_orders.patient_id');

        $totalWithVisits = $returning + $newWithVisits;
        $returnRate = $totalWithVisits > 0
            ? round(($returning / $totalWithVisits) * 100, 1)
            : 0;

        // Average service orders per returning patient in the period.
        $avgVisits = 0;
        if ($returning > 0) {
            $avgVisits = round(
                ServiceOrder::query()
                    ->whereBetween('service_orders.created_at', [$startDate, $endDate])
                    ->whereNotNull('patient_id')
                    ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
                    ->where('patients.created_at', '<', $startDate)
                    ->whereNull('patients.deleted_at')
                    ->count() / $returning,
                1
            );
        }

        // Trend: daily returning patient count for sparkline.
        $trend = ServiceOrder::query()
            ->whereBetween('service_orders.created_at', [$startDate, $endDate])
            ->whereNotNull('patient_id')
            ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
            ->where('patients.created_at', '<', $startDate)
            ->whereNull('patients.deleted_at')
            ->select(DB::raw('DATE(service_orders.created_at) as date'), DB::raw('COUNT(DISTINCT service_orders.patient_id) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Returning Patients', NumberHelper::moneyfy($returning))
                ->description('Seen before, revisited in period')
                ->color($returning > 0 ? 'success' : 'gray')
                ->chart($trend),

            Stat::make('Return Rate', $returnRate.'%')
                ->description($returning.' returning / '.$newWithVisits.' new with visits')
                ->color($returnRate >= 50 ? 'success' : ($returnRate >= 25 ? 'warning' : 'danger')),

            Stat::make('Avg Visits per Returning Patient', $avgVisits.'×')
                ->description('Service orders per returning patient in period')
                ->color('info'),
        ];
    }
}
