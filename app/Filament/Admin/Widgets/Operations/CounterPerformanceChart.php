<?php

namespace App\Filament\Admin\Widgets\Operations;

use App\Models\Closing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class CounterPerformanceChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Counter Performance';

    protected ?string $description = 'Closing amount collected per reception counter in the period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 4;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = Closing::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('closings.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('closings.created_at', '<=', $endDate))
            ->join('receptions', 'closings.reception_id', '=', 'receptions.id')
            ->selectRaw('receptions.name, SUM(closings.closing_amount_cash) as cash, SUM(closings.closing_amount_cheque) as cheque, SUM(closings.closing_amount_card) as card')
            ->groupBy('receptions.id', 'receptions.name')
            ->orderByDesc('cash')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cash',
                    'data' => $data->pluck('cash')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'stack' => 'total',
                ],
                [
                    'label' => 'Cheque',
                    'data' => $data->pluck('cheque')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'stack' => 'total',
                ],
                [
                    'label' => 'Card',
                    'data' => $data->pluck('card')->toArray(),
                    'backgroundColor' => 'rgba(168, 85, 247, 0.8)',
                    'stack' => 'total',
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
            'plugins' => ['legend' => ['display' => true]],
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
        ];
    }
}
