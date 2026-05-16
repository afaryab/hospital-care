<?php

namespace App\Filament\Admin\Widgets\HR;

use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class StaffActivityChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Staff Clinical Activity';

    protected ?string $description = 'Treatment records created per day by clinical staff in the period';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        // Daily treatment records created — proxy for clinical staff activity
        $rows = TreatmentRecord::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt', 'date');

        $dates = [];
        $values = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dates[] = $d->format('M d');
            $values[] = $rows->get($key, 0);
        }

        return [
            'datasets' => [[
                'label' => 'Treatment Records',
                'data' => $values,
                'borderColor' => 'rgba(34, 197, 94, 1)',
                'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                'borderWidth' => 2,
                'fill' => true,
                'tension' => 0.4,
                'pointRadius' => 3,
            ]],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
