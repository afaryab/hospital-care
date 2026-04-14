<?php

namespace App\Filament\Admin\Widgets\Operations;

use App\Models\ExpenseVoucher;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ExpenseVoucherTrend extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Expense Voucher Trend';

    protected ?string $description = 'Daily expense voucher amounts issued in the period';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $issued = ExpenseVoucher::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Amount Issued (PKR)',
                    'data' => $issued->pluck('total')->toArray(),
                    'borderColor' => 'rgba(249, 115, 22, 1)',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.2)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Count',
                    'data' => $issued->pluck('count')->toArray(),
                    'borderColor' => 'rgba(168, 85, 247, 1)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $issued->map(fn ($r) => Carbon::parse($r->date)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }
}
