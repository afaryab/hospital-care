<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Closing;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MigrationStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 'full';

    public function getColumns(): int
    {
        return 4;
    }

    public function getStats(): array
    {
        $oldCounts = $this->getOldDbCounts();

        $entities = [
            ['label' => 'Users', 'new' => User::count(), 'old' => $oldCounts['users']],
            ['label' => 'Patients', 'new' => Patient::count(), 'old' => $oldCounts['patients']],
            ['label' => 'Closings', 'new' => Closing::count(), 'old' => $oldCounts['closings']],
            ['label' => 'Transactions', 'new' => Transaction::count(), 'old' => $oldCounts['transactions']],
            ['label' => 'Services', 'new' => Service::count(), 'old' => $oldCounts['services']],
            ['label' => 'Exp. Vouchers', 'new' => ExpenseVoucher::count(), 'old' => $oldCounts['vouchers']],
            ['label' => 'Treatments', 'new' => ServiceOrder::count(), 'old' => $oldCounts['treatments']],
            ['label' => 'Receptions', 'new' => Reception::count(), 'old' => $oldCounts['receptions']],
            ['label' => 'Panels', 'new' => Panel::count(), 'old' => $oldCounts['panels']],
            ['label' => 'Exp. Categories', 'new' => ExpenseCategory::count(), 'old' => $oldCounts['expense_categories']],
        ];

        $totalOld = array_sum(array_column($entities, 'old'));
        $totalNew = array_sum(array_column($entities, 'new'));
        $overallPct = $totalOld > 0 ? round(($totalNew / $totalOld) * 100, 1) : 0;

        $stats = [
            StatsOverviewWidget\Stat::make('Overall Migration', "{$overallPct}%")
                ->description(number_format($totalNew).' / '.number_format($totalOld).' records')
                ->color($overallPct >= 100 ? 'success' : ($overallPct > 0 ? 'warning' : 'gray'))
                ->chart($this->buildMiniChart($entities)),
        ];

        foreach ($entities as $entity) {
            $pct = $entity['old'] > 0 ? round(($entity['new'] / $entity['old']) * 100, 1) : 0;
            $remaining = max(0, $entity['old'] - $entity['new']);
            $color = $pct >= 100 ? 'success' : ($pct > 0 ? 'warning' : 'gray');

            $stats[] = StatsOverviewWidget\Stat::make($entity['label'], "{$pct}%")
                ->description(number_format($entity['new']).' / '.number_format($entity['old']).' · '.number_format($remaining).' left')
                ->color($color);
        }

        return $stats;
    }

    /**
     * @return array<string, int>
     */
    private function getOldDbCounts(): array
    {
        return Cache::remember('migration_old_db_counts', 300, function (): array {
            try {
                $db = DB::connection('secondary');

                $serviceCount = 0;
                foreach (['opd_services', 'inpd_services', 'emergency_services', 'dental_services', 'test_services', 'ultrasound_services', 'xray_services'] as $table) {
                    $serviceCount += $db->table($table)->count();
                }

                $treatmentCount = 0;
                foreach (['opd_treatments', 'dental_treatments', 'emergency_treatments', 'ultrasound_treatments', 'xray_treatments', 'test_treatments', 'inpatient_treatments', 'recestation_treatments'] as $table) {
                    $treatmentCount += $db->table($table)->count();
                }

                return [
                    'users' => $db->table('aauth_users')->count(),
                    'patients' => $db->table('patients')->count(),
                    'closings' => $db->table('reception_counters_closings')->count(),
                    'transactions' => $db->table('reception_counters_closings_transactions')->count(),
                    'services' => $serviceCount,
                    'vouchers' => $db->table('expense_vouchers')->count(),
                    'treatments' => $treatmentCount,
                    'receptions' => $db->table('reception_counters')->count(),
                    'panels' => $db->table('panel_companies')->where('is_deleted', 0)->count(),
                    'expense_categories' => $db->table('expenses_categories')->count(),
                ];
            } catch (\Exception) {
                return array_fill_keys([
                    'users', 'patients', 'closings', 'transactions', 'services',
                    'vouchers', 'treatments', 'receptions', 'panels', 'expense_categories',
                ], 0);
            }
        });
    }

    /**
     * @param  array<int, array{label: string, new: int, old: int}>  $entities
     * @return array<int, int>
     */
    private function buildMiniChart(array $entities): array
    {
        return array_map(
            fn (array $e): int => $e['old'] > 0 ? (int) round(($e['new'] / $e['old']) * 100) : 0,
            $entities,
        );
    }
}
