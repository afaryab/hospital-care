<?php

namespace App\Filament\Admin\Widgets\Executive;

use App\Models\TransactionElement;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class DepartmentIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Income by Department';

    protected ?string $description = 'Revenue breakdown per service department';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = TransactionElement::query()
            ->where('transaction_elements.income_or_expense', 'INCOME')
            ->whereNotNull('transaction_elements.service_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('transaction_elements.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('transaction_elements.created_at', '<=', $endDate))
            ->join('services', 'transaction_elements.service_id', '=', 'services.id')
            ->join('service_departments', 'services.service_department_id', '=', 'service_departments.id')
            ->selectRaw('service_departments.name as dept, SUM(transaction_elements.amount) as total')
            ->groupBy('service_departments.id', 'service_departments.name')
            ->orderByDesc('total')
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderColor' => array_map(fn ($c) => str_replace('0.8', '1', $c), array_slice($colors, 0, $data->count())),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('dept')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
