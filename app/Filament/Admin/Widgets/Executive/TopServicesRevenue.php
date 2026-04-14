<?php

namespace App\Filament\Admin\Widgets\Executive;

use App\Models\TransactionElement;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class TopServicesRevenue extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Top 10 Services by Revenue';

    protected ?string $description = 'Highest earning services in the selected period';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 4;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = TransactionElement::query()
            ->where('income_or_expense', 'INCOME')
            ->whereNotNull('service_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('transaction_elements.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('transaction_elements.created_at', '<=', $endDate))
            ->join('services', 'transaction_elements.service_id', '=', 'services.id')
            ->selectRaw('services.name, SUM(transaction_elements.amount) as total')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(236, 72, 153, 0.8)',
            'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
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
