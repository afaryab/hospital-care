<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Models\ServiceOrder;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class PatientsByDepartmentChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Patient Visits by Department';

    protected ?string $description = 'Service orders placed per department in the selected period';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 4;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $data = ServiceOrder::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('service_orders.created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('service_orders.created_at', '<=', $endDate))
            ->join('services', 'service_orders.service_id', '=', 'services.id')
            ->join('service_departments', 'services.service_department_id', '=', 'service_departments.id')
            ->selectRaw('service_departments.name as dept, COUNT(DISTINCT service_orders.patient_id) as patients')
            ->groupBy('service_departments.id', 'service_departments.name')
            ->orderByDesc('patients')
            ->get();

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Unique Patients',
                    'data' => $data->pluck('patients')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('dept')->toArray(),
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
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
