<?php

namespace App\Filament\Admin\Widgets\Triage;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use App\Models\Triage;
use App\Models\TriageHistory;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TriageDistributionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Triage Distribution';

    protected ?string $description = 'Click a segment to view its service orders';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = '15s';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $triages = Triage::query()->where('is_active', true)->orderBy('priority')->get();

        $counts = TriageHistory::query()
            ->whereBetween('changed_at', [$start, $end])
            ->selectRaw('new_triage_id, COUNT(*) as cnt')
            ->groupBy('new_triage_id')
            ->pluck('cnt', 'new_triage_id');

        $colorMap = [
            'red' => 'rgba(239, 68, 68, 0.85)',
            'yellow' => 'rgba(234, 179, 8, 0.85)',
            'blue' => 'rgba(59, 130, 246, 0.85)',
            'sky' => 'rgba(14, 165, 233, 0.85)',
            'green' => 'rgba(34, 197, 94, 0.85)',
        ];

        return [
            'datasets' => [[
                'data' => $triages->map(fn (Triage $t) => (int) ($counts[$t->id] ?? 0))->toArray(),
                'backgroundColor' => $triages->map(fn (Triage $t) => $colorMap[$t->color] ?? 'rgba(148, 163, 184, 0.85)')->toArray(),
                'borderWidth' => 2,
            ]],
            'labels' => $triages->pluck('name')->toArray(),
            // Non-standard key, ignored by Chart.js itself but read back out of
            // chart.data in the onClick handler below to resolve which triage
            // was clicked.
            'triageIds' => $triages->pluck('id')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs
    {
        $baseUrl = ServiceOrderResource::getUrl('index');

        return RawJs::make(<<<JS
        {
            plugins: { legend: { display: true, position: 'bottom' } },
            onClick: (event, elements, chart) => {
                if (!elements.length) { return; }
                const triageId = chart.data.triageIds[elements[0].index];
                if (triageId) {
                    window.location.href = '{$baseUrl}?triage=' + triageId;
                }
            },
        }
        JS);
    }
}
