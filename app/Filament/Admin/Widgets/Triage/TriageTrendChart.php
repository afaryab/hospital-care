<?php

namespace App\Filament\Admin\Widgets\Triage;

use App\Models\Triage;
use App\Models\TriageHistory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class TriageTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Triage Assignments Over Time';

    protected ?string $description = 'Daily triage assignments in the selected period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    protected ?string $maxHeight = '300px';

    private const COLOR_MAP = [
        'red' => 'rgba(239, 68, 68, 0.9)',
        'yellow' => 'rgba(234, 179, 8, 0.9)',
        'blue' => 'rgba(59, 130, 246, 0.9)',
        'sky' => 'rgba(14, 165, 233, 0.9)',
        'green' => 'rgba(34, 197, 94, 0.9)',
    ];

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth())->startOfDay();
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now())->endOfDay();

        $days = collect(CarbonPeriod::create($start, $end))->map(fn (Carbon $d) => $d->toDateString());

        $triages = Triage::query()->where('is_active', true)->orderBy('priority')->get();

        $rows = TriageHistory::query()
            ->whereBetween('changed_at', [$start, $end])
            ->selectRaw('new_triage_id, DATE(changed_at) as day, COUNT(*) as cnt')
            ->groupBy('new_triage_id', DB::raw('DATE(changed_at)'))
            ->get()
            ->groupBy('new_triage_id');

        $datasets = $triages->map(function (Triage $triage) use ($rows, $days) {
            $byDay = ($rows[$triage->id] ?? collect())->pluck('cnt', 'day');

            return [
                'label' => $triage->name,
                'data' => $days->map(fn ($day) => (int) ($byDay[$day] ?? 0))->toArray(),
                'borderColor' => self::COLOR_MAP[$triage->color] ?? 'rgba(148, 163, 184, 0.9)',
                'backgroundColor' => 'transparent',
                'tension' => 0.3,
            ];
        })->toArray();

        return [
            'datasets' => $datasets,
            'labels' => $days->map(fn ($d) => Carbon::parse($d)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
