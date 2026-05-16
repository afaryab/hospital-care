<?php

namespace App\Filament\Admin\Widgets\History;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

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
        return Cache::remember('dashboard.history.revenue_by_year', 3600, function () {
            $rows = Transaction::query()
                ->selectRaw(
                    "YEAR(created_at) as year,
                     SUM(CASE WHEN income_or_expense = 'INCOME' THEN amount ELSE 0 END) as revenue,
                     SUM(CASE WHEN income_or_expense = 'EXPENSE' THEN amount ELSE 0 END) as expenses"
                )
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Revenue',
                        'data' => $rows->pluck('revenue')->toArray(),
                        'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                        'borderColor' => 'rgba(34, 197, 94, 1)',
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => 'Expenses',
                        'data' => $rows->pluck('expenses')->toArray(),
                        'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                        'borderColor' => 'rgba(239, 68, 68, 1)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $rows->pluck('year')->toArray(),
            ];
        });
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
