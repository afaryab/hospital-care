<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class PanelIncomeBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Income by Panel / Insurance';

    protected ?string $description = 'Revenue split between cash patients and panel/insurance';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 2;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $panelData = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->whereNotNull('panel_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('transactions.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('transactions.created_at', '<=', $endDate))
            ->join('panels', 'transactions.panel_id', '=', 'panels.id')
            ->selectRaw('panels.name, SUM(transactions.amount) as total')
            ->groupBy('panels.id', 'panels.name')
            ->orderByDesc('total')
            ->get();

        $cashIncome = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->whereNull('panel_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $labels = $panelData->pluck('name')->prepend('Cash')->toArray();
        $data = $panelData->pluck('total')->prepend($cashIncome)->toArray();

        $colors = [
            'rgba(34, 197, 94, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(20, 184, 166, 0.8)',
            'rgba(236, 72, 153, 0.8)', 'rgba(99, 102, 241, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
        ];
    }
}
