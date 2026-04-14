<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class IncomeVsExpenseTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Income vs Expense (Stacked)';

    protected ?string $description = 'Stacked comparison of income and expense in the period';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $income = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $expense = Transaction::query()
            ->where('income_or_expense', 'EXPENSE')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $allDates = $income->keys()->merge($expense->keys())->unique()->sort()->values();

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $allDates->map(fn ($d) => $income->get($d, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'stack' => 'combined',
                ],
                [
                    'label' => 'Expense',
                    'data' => $allDates->map(fn ($d) => $expense->get($d, 0))->values()->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'stack' => 'combined',
                ],
            ],
            'labels' => $allDates->map(fn ($d) => Carbon::parse($d)->format('M d'))->toArray(),
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
