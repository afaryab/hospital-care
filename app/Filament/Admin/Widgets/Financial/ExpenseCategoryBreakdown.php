<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\ExpenseVoucher;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ExpenseCategoryBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Expenses by Category';

    protected ?string $description = 'Expense vouchers grouped by category';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('expense_vouchers.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('expense_vouchers.created_at', '<=', $endDate))
            ->join('expense_categories', 'expense_vouchers.exp_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name, SUM(expense_vouchers.amount) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $colors = [
            'rgba(239, 68, 68, 0.8)', 'rgba(234, 179, 8, 0.8)', 'rgba(249, 115, 22, 0.8)',
            'rgba(168, 85, 247, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(20, 184, 166, 0.8)',
            'rgba(236, 72, 153, 0.8)', 'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
        ];
    }
}
