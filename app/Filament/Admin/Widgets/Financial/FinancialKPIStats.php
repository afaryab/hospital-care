<?php

namespace App\Filament\Admin\Widgets\Financial;

use App\Helpers\NumberHelper;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Receaveable;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class FinancialKPIStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->pageFilters['endDate'] ?? Carbon::now();

        $grossRevenue = Transaction::query()
            ->where('income_or_expense', 'INCOME')
            ->where('is_refunded', false)
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $totalExpenses = Transaction::query()
            ->where('income_or_expense', 'EXPENSE')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $netIncome = $grossRevenue - $totalExpenses;

        $closingTotals = Closing::query()
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->selectRaw('SUM(closing_amount_cash) as cash, SUM(closing_amount_cheque + closing_amount_card) as card_cheque')
            ->first();
        $cashCollected = $closingTotals->cash ?? 0;
        $cardChequeCollected = $closingTotals->card_cheque ?? 0;

        $refunds = Transaction::query()
            ->where('is_refunded', true)
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        $outstandingReceivables = Receaveable::query()
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->sum('amount');

        $pendingVouchers = ExpenseVoucher::query()
            ->whereNull('transaction_id')
            ->when($startDate, fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate))
            ->sum('amount');

        return [
            Stat::make('Gross Revenue', 'PKR '.NumberHelper::moneyfy($grossRevenue))
                ->description('Total income in period')
                ->color('success'),

            Stat::make('Total Expenses', 'PKR '.NumberHelper::moneyfy($totalExpenses))
                ->description('Vouchers: PKR '.NumberHelper::moneyfy($pendingVouchers).' pending')
                ->color('danger'),

            Stat::make('Net Income', 'PKR '.NumberHelper::moneyfy($netIncome))
                ->description('Revenue minus expenses')
                ->color($netIncome >= 0 ? 'success' : 'danger'),

            Stat::make('Cash Collected', 'PKR '.NumberHelper::moneyfy($cashCollected))
                ->description('Card + Cheque: PKR '.NumberHelper::moneyfy($cardChequeCollected))
                ->color('info'),

            Stat::make('Refunds Issued', 'PKR '.NumberHelper::moneyfy($refunds))
                ->description('Total refunded transactions')
                ->color('warning'),

            Stat::make('Outstanding Receivables', 'PKR '.NumberHelper::moneyfy($outstandingReceivables))
                ->description('All-time unpaid receivables')
                ->color('warning'),
        ];
    }
}
