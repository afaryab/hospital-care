<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Models\ServiceOrder;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class NewVsReturningChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'New vs Returning Patients';

    protected ?string $description = 'Daily breakdown of first-time vs returning patient visits in the period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $startDate = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($this->pageFilters['endDate'] ?? now());

        // Group by date: count distinct new vs returning patients per day.
        $returningByDay = ServiceOrder::query()
            ->whereBetween('service_orders.created_at', [$startDate, $endDate])
            ->whereNotNull('service_orders.patient_id')
            ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
            ->where('patients.created_at', '<', $startDate)
            ->whereNull('patients.deleted_at')
            ->select(DB::raw('DATE(service_orders.created_at) as date'), DB::raw('COUNT(DISTINCT service_orders.patient_id) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $newByDay = ServiceOrder::query()
            ->whereBetween('service_orders.created_at', [$startDate, $endDate])
            ->whereNotNull('service_orders.patient_id')
            ->join('patients', 'service_orders.patient_id', '=', 'patients.id')
            ->whereBetween('patients.created_at', [$startDate, $endDate])
            ->whereNull('patients.deleted_at')
            ->select(DB::raw('DATE(service_orders.created_at) as date'), DB::raw('COUNT(DISTINCT service_orders.patient_id) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $allDates = $returningByDay->keys()->merge($newByDay->keys())->unique()->sort()->values();

        return [
            'datasets' => [
                [
                    'label' => 'Returning',
                    'data' => $allDates->map(fn ($d) => $returningByDay->get($d, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                    'stack' => 'patients',
                ],
                [
                    'label' => 'New',
                    'data' => $allDates->map(fn ($d) => $newByDay->get($d, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'borderWidth' => 2,
                    'stack' => 'patients',
                ],
            ],
            'labels' => $allDates->map(fn ($d) => Carbon::parse($d)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true]],
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
