<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class MonthlyRevenueTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Monthly Revenue Trend (12 Months)';

    protected ?string $description = 'Rolling 12-month revenue and expense overview';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->subMonths($i));

        $income = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $expense = Transaction::query()
            ->where('income_or_expense', 'EXPENSE')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $months->map(fn ($m) => $income->get($m->format('Y-m'), 0))->values()->toArray(),
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $months->map(fn ($m) => $expense->get($m->format('Y-m'), 0))->values()->toArray(),
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
