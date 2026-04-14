<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\Closing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class CounterCollectionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Collection by Counter / Reception';

    protected ?string $description = 'Total closing amounts grouped by reception counter';

    protected static ?int $sort = 7;

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
            ->selectRaw('receptions.name, SUM(closings.closing_amount) as total_collected, SUM(closings.expense_payed) as total_expense, COUNT(closings.id) as sessions')
            ->groupBy('receptions.id', 'receptions.name')
            ->orderByDesc('total_collected')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Collected',
                    'data' => $data->pluck('total_collected')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                ],
                [
                    'label' => 'Expenses Paid',
                    'data' => $data->pluck('total_expense')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
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
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
