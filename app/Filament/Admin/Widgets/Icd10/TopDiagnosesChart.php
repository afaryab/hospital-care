<?php

namespace App\Filament\Admin\Widgets\Icd10;

use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TopDiagnosesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Top 10 Diagnoses';

    protected ?string $description = 'Most frequently recorded ICD-10 codes in the period';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $rows = TreatmentRecord::query()
            ->whereNotNull('icd10_code_id')
            ->whereDate('treatment_records.created_at', '>=', $start)
            ->whereDate('treatment_records.created_at', '<=', $end)
            ->join('icd10_codes', 'treatment_records.icd10_code_id', '=', 'icd10_codes.id')
            ->select(
                'icd10_codes.code',
                'icd10_codes.description',
                DB::raw('COUNT(*) as cnt')
            )
            ->groupBy('icd10_codes.id', 'icd10_codes.code', 'icd10_codes.description')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(236, 72, 153, 0.8)',
            'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [[
                'label' => 'Cases',
                'data' => $rows->pluck('cnt')->toArray(),
                'backgroundColor' => array_slice($colors, 0, $rows->count()),
                'borderWidth' => 1,
            ]],
            'labels' => $rows->map(fn ($r) => "{$r->code}: {$r->description}")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
