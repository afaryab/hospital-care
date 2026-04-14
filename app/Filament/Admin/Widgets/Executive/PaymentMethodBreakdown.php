<?php

namespace App\Filament\Admin\Widgets\Executive;

use App\Models\Closing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethodBreakdown extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Payment Method Breakdown';

    protected ?string $description = 'Cash vs Cheque vs Card collections';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 2;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $totals = Closing::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('SUM(closing_amount_cash) as cash, SUM(closing_amount_cheque) as cheque, SUM(closing_amount_card) as card')
            ->first();

        $cash = round($totals->cash ?? 0, 2);
        $cheque = round($totals->cheque ?? 0, 2);
        $card = round($totals->card ?? 0, 2);

        return [
            'datasets' => [
                [
                    'data' => [$cash, $cheque, $card],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(168, 85, 247, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Cash', 'Cheque', 'Card'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
            ],
        ];
    }
}
