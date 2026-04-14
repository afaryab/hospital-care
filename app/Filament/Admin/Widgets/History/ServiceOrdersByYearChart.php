<?php

namespace App\Filament\Admin\Widgets\History;

use App\Models\ServiceOrder;
use Filament\Widgets\ChartWidget;

class ServiceOrdersByYearChart extends ChartWidget
{
    protected ?string $heading = 'Service Orders by Year';

    protected ?string $description = 'Annual service order volume (all-time, including deleted)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $byType = ServiceOrder::withTrashed()
            ->selectRaw('YEAR(created_at) as year, type, COUNT(*) as count')
            ->groupBy('year', 'type')
            ->orderBy('year')
            ->get()
            ->groupBy('year');

        $years = $byType->keys()->sort()->values();
        $types = ServiceOrder::withTrashed()->distinct('type')->pluck('type')->sort()->values();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)',
        ];

        $datasets = $types->values()->map(fn ($type, $i) => [
            'label' => $type,
            'data' => $years->map(fn ($year) => $byType->get($year)?->where('type', $type)->sum('count') ?? 0)->values()->toArray(),
            'backgroundColor' => $colors[$i % count($colors)],
            'stack' => 'total',
        ])->values()->toArray();

        return [
            'datasets' => $datasets,
            'labels' => $years->toArray(),
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
