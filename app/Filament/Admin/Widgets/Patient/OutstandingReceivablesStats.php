<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Models\Receaveable;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class OutstandingReceivablesStats extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Top 10 Patients by Outstanding Balance';

    protected ?string $description = 'Patients with the highest unpaid receivable amounts';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = Receaveable::query()
            ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
            ->join('patients', 'receaveables.patient_id', '=', 'patients.id')
            ->selectRaw('patients.name, SUM(receaveables.amount) as total_owed')
            ->groupBy('patients.id', 'patients.name')
            ->orderByDesc('total_owed')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(239, 68, 68, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(234, 179, 8, 0.8)',
            'rgba(168, 85, 247, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(99, 102, 241, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(236, 72, 153, 0.8)',
            'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Outstanding (PKR)',
                    'data' => $data->pluck('total_owed')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
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
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['beginAtZero' => true]],
        ];
    }
}
