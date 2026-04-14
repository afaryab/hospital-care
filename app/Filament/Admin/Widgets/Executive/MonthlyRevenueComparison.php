<?php

namespace App\Filament\Admin\Widgets\Executive;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class MonthlyRevenueComparison extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Net Income by Day';

    protected ?string $description = 'Daily net income (revenue minus expenses) in the selected period';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

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

        $netData = $allDates->map(fn ($d) => round($income->get($d, 0) - $expense->get($d, 0), 2))->values()->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Net Income',
                    'data' => $netData,
                    'backgroundColor' => array_map(
                        fn ($v) => $v >= 0 ? 'rgba(34, 197, 94, 0.7)' : 'rgba(239, 68, 68, 0.7)',
                        $netData
                    ),
                    'borderColor' => array_map(
                        fn ($v) => $v >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)',
                        $netData
                    ),
                    'borderWidth' => 1,
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
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
