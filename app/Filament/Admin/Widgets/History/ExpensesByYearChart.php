<?php

namespace App\Filament\Admin\Widgets\History;

use App\Models\ExpenseVoucher;
use Filament\Widgets\ChartWidget;

class ExpensesByYearChart extends ChartWidget
{
    protected ?string $heading = 'Expenses by Year';

    protected ?string $description = 'Annual expense voucher totals (all-time)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = ExpenseVoucher::query()
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Expense Amount (PKR)',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Voucher Count',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => 'rgba(234, 179, 8, 1)',
                    'backgroundColor' => 'rgba(234, 179, 8, 0)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data->pluck('year')->toArray(),
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
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
