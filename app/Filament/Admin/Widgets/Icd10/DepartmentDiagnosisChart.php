<?php

namespace App\Filament\Admin\Widgets\Icd10;

use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DepartmentDiagnosisChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Diagnoses by Department';

    protected ?string $description = 'ICD-10 coded records grouped by service department';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $rows = TreatmentRecord::query()
            ->whereNotNull('treatment_records.icd10_code_id')
            ->whereDate('treatment_records.created_at', '>=', $start)
            ->whereDate('treatment_records.created_at', '<=', $end)
            ->join('service_orders', 'treatment_records.service_order_id', '=', 'service_orders.id')
            ->join('service_departments', 'service_orders.doctor_id', '=', 'service_departments.id')
            ->select('service_departments.name as dept', DB::raw('COUNT(*) as cnt'))
            ->groupBy('service_departments.id', 'service_departments.name')
            ->orderByDesc('cnt')
            ->get();

        // Fallback: group by SO type if department join returns nothing
        if ($rows->isEmpty()) {
            $rows = TreatmentRecord::query()
                ->whereNotNull('treatment_records.icd10_code_id')
                ->whereDate('treatment_records.created_at', '>=', $start)
                ->whereDate('treatment_records.created_at', '<=', $end)
                ->join('service_orders', 'treatment_records.service_order_id', '=', 'service_orders.id')
                ->select('service_orders.type as dept', DB::raw('COUNT(*) as cnt'))
                ->groupBy('service_orders.type')
                ->orderByDesc('cnt')
                ->get();
        }

        $colors = [
            'rgba(99, 102, 241, 0.7)', 'rgba(34, 197, 94, 0.7)', 'rgba(239, 68, 68, 0.7)',
            'rgba(234, 179, 8, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(168, 85, 247, 0.7)',
            'rgba(20, 184, 166, 0.7)', 'rgba(249, 115, 22, 0.7)',
        ];

        return [
            'datasets' => [[
                'label' => 'Diagnoses',
                'data' => $rows->pluck('cnt')->toArray(),
                'backgroundColor' => array_slice($colors, 0, $rows->count()),
                'borderWidth' => 1,
            ]],
            'labels' => $rows->pluck('dept')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
