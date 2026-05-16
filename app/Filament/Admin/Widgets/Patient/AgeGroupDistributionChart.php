<?php

namespace App\Filament\Admin\Widgets\Patient;

use App\Models\Patient;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class AgeGroupDistributionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Patient Age Group Distribution';

    protected ?string $description = 'Patients grouped by age range in the selected period';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $patients = Patient::query()
            ->whereNotNull('age_dob')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('TIMESTAMPDIFF(YEAR, age_dob, CURDATE()) as age')
            ->get();

        $groups = [
            '0–12 (Child)' => $patients->whereBetween('age', [0, 12])->count(),
            '13–17 (Teen)' => $patients->whereBetween('age', [13, 17])->count(),
            '18–35 (Young Adult)' => $patients->whereBetween('age', [18, 35])->count(),
            '36–60 (Adult)' => $patients->whereBetween('age', [36, 60])->count(),
            '61–80 (Senior)' => $patients->whereBetween('age', [61, 80])->count(),
            '81+ (Elderly)' => $patients->where('age', '>', 80)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Patients',
                    'data' => array_values($groups),
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.8)', 'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)', 'rgba(234, 179, 8, 0.8)',
                        'rgba(249, 115, 22, 0.8)', 'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => array_keys($groups),
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
