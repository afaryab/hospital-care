<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\ExpenseVoucher;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ExpenseVoucherStatus extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Expense Voucher Status';

    protected ?string $description = 'Paid vs pending expense vouchers in period';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 2;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $paid = ExpenseVoucher::query()
            ->whereNotNull('transaction_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $pending = ExpenseVoucher::query()
            ->whereNull('transaction_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'data' => [$paid, $pending],
                    'backgroundColor' => ['rgba(34, 197, 94, 0.8)', 'rgba(234, 179, 8, 0.8)'],
                    'borderColor' => ['rgba(34, 197, 94, 1)', 'rgba(234, 179, 8, 1)'],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Paid', 'Pending'],
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
