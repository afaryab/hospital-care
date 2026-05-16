<?php

namespace App\Filament\Admin\Widgets\Icd10;

use App\Helpers\NumberHelper;
use App\Models\Icd10Code;
use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Icd10KPIStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $start = Carbon::parse($this->pageFilters['startDate'] ?? now()->startOfMonth());
        $end = Carbon::parse($this->pageFilters['endDate'] ?? now());

        $base = TreatmentRecord::query()
            ->whereNotNull('icd10_code_id')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        $totalDiagnoses = $base->count();
        $uniqueCodes = (clone $base)->distinct('icd10_code_id')->count('icd10_code_id');
        $finalized = (clone $base)->where('is_finalized', true)->count();

        // Top code in period
        $topRow = (clone $base)
            ->select('icd10_code_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('icd10_code_id')
            ->orderByDesc('cnt')
            ->first();

        $topCode = $topRow ? Icd10Code::find($topRow->icd10_code_id) : null;
        $topLabel = $topCode
            ? "{$topCode->code} — {$topCode->description} ({$topRow->cnt}×)"
            : 'No data';

        // Trend sparkline (daily count)
        $trend = (clone $base)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt')
            ->toArray();

        return [
            Stat::make('Total Diagnoses', NumberHelper::moneyfy($totalDiagnoses))
                ->description('ICD-10 coded treatment records in period')
                ->color('primary')
                ->chart($trend),

            Stat::make('Unique Codes Used', NumberHelper::moneyfy($uniqueCodes))
                ->description("out of {$totalDiagnoses} total records")
                ->color('info'),

            Stat::make('Finalized Records', NumberHelper::moneyfy($finalized))
                ->description(
                    $totalDiagnoses > 0
                        ? round(($finalized / $totalDiagnoses) * 100, 1).'% finalization rate'
                        : 'No records'
                )
                ->color($finalized > 0 ? 'success' : 'gray'),

            Stat::make('Top Diagnosis', '')
                ->description($topLabel)
                ->color('warning'),
        ];
    }
}
