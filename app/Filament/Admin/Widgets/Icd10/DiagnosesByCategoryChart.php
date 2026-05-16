<?php

namespace App\Filament\Admin\Widgets\Icd10;

use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DiagnosesByCategoryChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Diagnoses by Category';

    protected ?string $description = 'ICD-10 category distribution in the period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $rows = TreatmentRecord::query()
            ->whereNotNull('icd10_code_id')
            ->whereDate('treatment_records.created_at', '>=', $start)
            ->whereDate('treatment_records.created_at', '<=', $end)
            ->join('icd10_codes', 'treatment_records.icd10_code_id', '=', 'icd10_codes.id')
            ->whereNotNull('icd10_codes.category')
            ->select('icd10_codes.category', DB::raw('COUNT(*) as cnt'))
            ->groupBy('icd10_codes.category')
            ->orderByDesc('cnt')
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(236, 72, 153, 0.8)',
            'rgba(6, 182, 212, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)',
        ];

        return [
            'datasets' => [[
                'data' => $rows->pluck('cnt')->toArray(),
                'backgroundColor' => array_slice($colors, 0, $rows->count()),
                'borderWidth' => 2,
            ]],
            'labels' => $rows->pluck('category')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
        ];
    }
}
