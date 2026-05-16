<?php

namespace App\Filament\Admin\Widgets\Icd10;

use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class DiagnosisTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Diagnosis Trend';

    protected ?string $description = 'Daily ICD-10 coded treatment records in the period';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $rows = TreatmentRecord::query()
            ->whereNotNull('icd10_code_id')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt', 'date');

        // Fill every day in range even if zero
        $dates = [];
        $values = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dates[] = $d->format('M d');
            $values[] = $rows->get($key, 0);
        }

        return [
            'datasets' => [[
                'label' => 'Diagnoses',
                'data' => $values,
                'borderColor' => 'rgba(99, 102, 241, 1)',
                'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4,
            ]],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
