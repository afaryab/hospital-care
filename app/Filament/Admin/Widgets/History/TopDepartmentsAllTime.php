<?php

namespace App\Filament\Admin\Widgets\History;

use App\Models\TransactionElement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TopDepartmentsAllTime extends ChartWidget
{
    protected ?string $heading = 'All-Time Revenue by Department';

    protected ?string $description = 'Total lifetime revenue per service department';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        return Cache::remember('dashboard.history.top_departments_all_time', 3600, function () {
            $data = TransactionElement::query()
                ->where('transaction_elements.income_or_expense', 'INCOME')
                ->whereNotNull('transaction_elements.service_id')
                ->join('services', 'transaction_elements.service_id', '=', 'services.id')
                ->join('service_departments', 'services.service_department_id', '=', 'service_departments.id')
                ->selectRaw('service_departments.name as dept, SUM(transaction_elements.amount) as total, COUNT(*) as transactions')
                ->groupBy('service_departments.id', 'service_departments.name')
                ->orderByDesc('total')
                ->get();

            $colors = [
                'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
                'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
                'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)',
            ];

            return [
                'datasets' => [
                    [
                        'label' => 'All-Time Revenue (PKR)',
                        'data' => $data->pluck('total')->toArray(),
                        'backgroundColor' => array_slice($colors, 0, $data->count()),
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $data->pluck('dept')->toArray(),
            ];
        });
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
