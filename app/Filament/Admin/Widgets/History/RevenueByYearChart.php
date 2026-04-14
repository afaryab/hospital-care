<?php

namespace App\Filament\Admin\Widgets\History;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class RevenueByYearChart extends ChartWidget
{
    protected ?string $heading = 'Annual Revenue & Expense History';

    protected ?string $description = 'Year-by-year total revenue and expense comparison (all-time)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $revenue = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $expenses = Transaction::query()
            ->where('income_or_expense', 'EXPENSE')
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $years = $revenue->keys()->merge($expenses->keys())->unique()->sort()->values();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $years->map(fn ($y) => $revenue->get($y, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $years->map(fn ($y) => $expenses->get($y, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 2,
                ],
            ],
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
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
