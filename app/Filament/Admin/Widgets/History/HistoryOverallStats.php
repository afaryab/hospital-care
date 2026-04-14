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

class HistoryOverallStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalRevenue = Transaction::where('income_or_expense', 'INCOME')->sum('amount');
        $totalExpenses = Transaction::where('income_or_expense', 'EXPENSE')->sum('amount');
        $netIncome = $totalRevenue - $totalExpenses;

        $totalPatients = Patient::count();
        $totalServiceOrders = ServiceOrder::withTrashed()->count();
        $totalClosings = Closing::count();

        $totalExpenseVouchers = ExpenseVoucher::count();
        $totalExpenseAmount = ExpenseVoucher::sum('amount');

        $totalReceivables = Receaveable::sum('orignal_amount');
        $unpaidReceivables = Receaveable::whereNotIn('status', ['paid', 'cancelled'])->sum('amount');

        $firstPatient = Patient::orderBy('created_at')->value('created_at');
        $operatingSince = $firstPatient ? Carbon::parse($firstPatient)->format('M Y') : 'N/A';

        $revenueTrend = Transaction::where('income_or_expense', 'INCOME')
            ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total')
            ->toArray();

        return [
            Stat::make('All-Time Revenue', 'PKR '.NumberHelper::moneyfy($totalRevenue))
                ->description('Since '.$operatingSince)
                ->color('success')
                ->chart($revenueTrend),

            Stat::make('All-Time Expenses', 'PKR '.NumberHelper::moneyfy($totalExpenses))
                ->description('Total expense vouchers: '.NumberHelper::moneyfy($totalExpenseVouchers))
                ->color('danger'),

            Stat::make('Net All-Time Income', 'PKR '.NumberHelper::moneyfy($netIncome))
                ->description('Revenue minus expenses')
                ->color($netIncome >= 0 ? 'success' : 'danger'),

            Stat::make('Total Patients', NumberHelper::moneyfy($totalPatients))
                ->description('All registered patients')
                ->color('primary'),

            Stat::make('Total Service Orders', NumberHelper::moneyfy($totalServiceOrders))
                ->description('Counter sessions: '.NumberHelper::moneyfy($totalClosings))
                ->color('info'),

            Stat::make('Total Receivables', 'PKR '.NumberHelper::moneyfy($totalReceivables))
                ->description('Unpaid: PKR '.NumberHelper::moneyfy($unpaidReceivables))
                ->color('warning'),
        ];
    }
}
