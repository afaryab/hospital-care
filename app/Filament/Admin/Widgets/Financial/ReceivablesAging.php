<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Models\Receaveable;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ReceivablesAging extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Receivables Aging';

    protected ?string $description = 'Outstanding receivables grouped by age (all-time unpaid)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $receivables = Receaveable::query()
            ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
            ->selectRaw('DATEDIFF(NOW(), created_at) as age, amount')
            ->get();

        $aging = [
            '0–30 days' => $receivables->where('age', '<=', 30)->sum('amount'),
            '31–60 days' => $receivables->whereBetween('age', [31, 60])->sum('amount'),
            '61–90 days' => $receivables->whereBetween('age', [61, 90])->sum('amount'),
            '90+ days' => $receivables->where('age', '>', 90)->sum('amount'),
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($aging),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_keys($aging),
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
