<?php

namespace App\Filament\Admin\Widgets\Triage;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use App\Models\Triage;
use App\Models\TriageHistory;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TriageKPIStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $triages = Triage::query()->where('is_active', true)->orderBy('priority')->get();

        $counts = TriageHistory::query()
            ->whereBetween('changed_at', [$start, $end])
            ->selectRaw('new_triage_id, COUNT(*) as cnt')
            ->groupBy('new_triage_id')
            ->pluck('cnt', 'new_triage_id');

        $stats = $triages->map(function (Triage $triage) use ($counts) {
            $count = (int) ($counts[$triage->id] ?? 0);

            return Stat::make($triage->name, (string) $count)
                ->description('Assigned in period')
                ->color(Color::all()[$triage->color] ?? 'gray')
                ->url(ServiceOrderResource::getUrl('index', ['triage' => $triage->id]));
        })->all();

        $stats[] = Stat::make('Total Triaged', (string) $counts->sum())
            ->description('All triage assignments in period')
            ->color('gray');

        return $stats;
    }
}
