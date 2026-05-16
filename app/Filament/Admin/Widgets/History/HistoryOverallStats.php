<?php

namespace App\Filament\Admin\Widgets\History;

use App\Helpers\NumberHelper;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class HistoryOverallStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // All-time aggregates change slowly; cache for 1 hour to avoid
        // heavy full-table scans on every dashboard load.
        $d = Cache::remember('dashboard.history.overall_stats', 3600, function () {
            $txTotals = Transaction::selectRaw(
                "SUM(CASE WHEN income_or_expense = 'INCOME' THEN amount ELSE 0 END) as revenue,
                 SUM(CASE WHEN income_or_expense = 'EXPENSE' THEN amount ELSE 0 END) as expenses"
            )->first();

            $evTotals = ExpenseVoucher::selectRaw('COUNT(*) as count, SUM(amount) as amount')->first();
            $recTotals = Receaveable::selectRaw('SUM(orignal_amount) as total, SUM(CASE WHEN status NOT IN (\'paid\',\'cancelled\') THEN amount ELSE 0 END) as unpaid')->first();

            return [
                'total_revenue' => $txTotals->revenue ?? 0,
                'total_expenses' => $txTotals->expenses ?? 0,
                'total_patients' => Patient::count(),
                'total_service_orders' => ServiceOrder::withTrashed()->count(),
                'total_closings' => Closing::count(),
                'total_expense_vouchers' => $evTotals->count ?? 0,
                'total_receivables' => $recTotals->total ?? 0,
                'unpaid_receivables' => $recTotals->unpaid ?? 0,
                'operating_since' => Patient::orderBy('created_at')->value('created_at'),
                'revenue_trend' => Transaction::where('income_or_expense', 'INCOME')
                    ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
                    ->groupBy('year')->orderBy('year')->pluck('total')->toArray(),
            ];
        });

        $netIncome = $d['total_revenue'] - $d['total_expenses'];
        $operatingSince = $d['operating_since'] ? Carbon::parse($d['operating_since'])->format('M Y') : 'N/A';

        return [
            Stat::make('All-Time Revenue', 'PKR '.NumberHelper::moneyfy($d['total_revenue']))
                ->description('Since '.$operatingSince)
                ->color('success')
                ->chart($d['revenue_trend']),

            Stat::make('All-Time Expenses', 'PKR '.NumberHelper::moneyfy($d['total_expenses']))
                ->description('Total expense vouchers: '.NumberHelper::moneyfy($d['total_expense_vouchers']))
                ->color('danger'),

            Stat::make('Net All-Time Income', 'PKR '.NumberHelper::moneyfy($netIncome))
                ->description('Revenue minus expenses')
                ->color($netIncome >= 0 ? 'success' : 'danger'),

            Stat::make('Total Patients', NumberHelper::moneyfy($d['total_patients']))
                ->description('All registered patients')
                ->color('primary'),

            Stat::make('Total Service Orders', NumberHelper::moneyfy($d['total_service_orders']))
                ->description('Counter sessions: '.NumberHelper::moneyfy($d['total_closings']))
                ->color('info'),

            Stat::make('Total Receivables', 'PKR '.NumberHelper::moneyfy($d['total_receivables']))
                ->description('Unpaid: PKR '.NumberHelper::moneyfy($d['unpaid_receivables']))
                ->color('warning'),
        ];
    }
}
