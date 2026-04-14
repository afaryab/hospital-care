<?php

namespace App\Filament\Admin\Widgets\Operations;

use App\Models\ServiceOrder;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class TopServicesUtilization extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Top 10 Most Utilized Services';

    protected ?string $description = 'Services with the highest number of orders in the period';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = ServiceOrder::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('service_orders.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('service_orders.created_at', '<=', $endDate))
            ->join('services', 'service_orders.service_id', '=', 'services.id')
            ->selectRaw('services.name, COUNT(*) as orders, COUNT(DISTINCT service_orders.patient_id) as patients')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('orders')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->pluck('orders')->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Unique Patients',
                    'data' => $data->pluck('patients')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
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
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
