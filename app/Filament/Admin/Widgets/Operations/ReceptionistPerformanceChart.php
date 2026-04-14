<?php

namespace App\Filament\Admin\Widgets\Operations;

use App\Models\Closing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ReceptionistPerformanceChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Receptionist Performance';

    protected ?string $description = 'Amount collected per receptionist in the selected period';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = Closing::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('closings.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('closings.created_at', '<=', $endDate))
            ->join('users', 'closings.receptionist_id', '=', 'users.id')
            ->selectRaw('users.name, SUM(closings.closing_amount) as total, COUNT(closings.id) as sessions')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(59, 130, 246, 0.8)',
            'rgba(168, 85, 247, 0.8)', 'rgba(20, 184, 166, 0.8)', 'rgba(234, 179, 8, 0.8)',
            'rgba(249, 115, 22, 0.8)', 'rgba(236, 72, 153, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Total Collected (PKR)',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
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
            'scales' => ['x' => ['beginAtZero' => true]],
        ];
    }
}
