<?php

namespace App\Services;

use App\Models\Closing;
use App\Models\HospitalSetting;
use Illuminate\Support\Carbon;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusIncoming;
use Processton\Abacus\Models\AbacusTransaction;
use Processton\Abacus\Models\AbacusYear;

class AbacusClosingService
{
    /** @var array<string, AbacusChartOfAccount> */
    protected array $coaCache = [];

    /** @var array<string, AbacusYear> */
    protected array $yearCache = [];

    public static function isAutoMapEnabled(): bool
    {
        return (bool) HospitalSetting::get('abacus_auto_map_accounts', false);
    }

    /**
     * Create AbacusIncoming and granular double-entry transactions for a closing.
     *
     * Entry categories:
     * - Cash Sales: INCOME tx without receivable → Dr Cash, Cr Service Revenue
     * - Credit Sales: INCOME tx that created a receivable → Dr Cash (paid), Dr A/R (unpaid), Cr Service Revenue (total)
     * - Receivable Payments: INCOME tx with receaveable_id → Dr Cash, Cr A/R
     * - Regular Expenses: EXPENSE tx (not refund/discount) → Dr Other Expenses, Cr Cash
     * - Refunds: EXPENSE tx with category type RFND → Dr Refunds, Cr Cash
     * - Discounts: EXPENSE tx with category type DISC → Dr Discounts, Cr Cash
     */
    public function createEntriesForClosing(Closing $closing): ?AbacusIncoming
    {
        if ($closing->abacusIncoming()->exists()) {
            return null;
        }

        $this->loadCoaCache();

        $cashInHand = $this->coaCache['1110'] ?? null;
        $accountsReceivable = $this->coaCache['1130'] ?? null;
        $serviceRevenue = $this->coaCache['4200'] ?? null;
        $refundsAccount = $this->coaCache['5010'] ?? null;
        $discountsAccount = $this->coaCache['5020'] ?? null;
        $otherExpenses = $this->coaCache['5900'] ?? null;

        if (! $cashInHand || ! $serviceRevenue || ! $otherExpenses) {
            throw new \RuntimeException('Abacus Chart of Accounts not seeded. Run ChartOfAccountsSeeder first.');
        }

        $closing->loadMissing([
            'transactions.expenseCategory',
            'transactions.receaveable',
            'reception',
        ]);

        $transactions = $closing->transactions;

        if ($transactions->isEmpty()) {
            return null;
        }

        $closingDate = $closing->created_at
            ? Carbon::parse($closing->created_at)
            : now();

        $yearModel = $this->resolveOrCreateFinancialYear($closingDate);

        // Categorize transactions
        $cashSales = 0.0;
        $creditSalesCash = 0.0;
        $creditSalesReceivable = 0.0;
        $receivablePayments = 0.0;
        $regularExpenses = 0.0;
        $refundExpenses = 0.0;
        $discountExpenses = 0.0;

        foreach ($transactions as $tx) {
            $amount = (float) $tx->amount;

            if ($tx->income_or_expense === 'INCOME') {
                if ($tx->receaveable_id) {
                    // Receivable payment: collecting cash against existing receivable
                    $receivablePayments += $amount;
                } elseif ($tx->receaveable && $tx->receaveable->orignal_amount > 0) {
                    // Credit sale: patient underpaid, receivable was created from this tx
                    $receivableAmount = (float) $tx->receaveable->orignal_amount;
                    $cashPortion = $amount - $receivableAmount;
                    if ($cashPortion > 0) {
                        $creditSalesCash += $cashPortion;
                    }
                    $creditSalesReceivable += $receivableAmount;
                } else {
                    // Pure cash sale
                    $cashSales += $amount;
                }
            } elseif ($tx->income_or_expense === 'EXPENSE') {
                $categoryType = $tx->expenseCategory?->type;

                if ($categoryType === 'RFND') {
                    $refundExpenses += $amount;
                } elseif ($categoryType === 'DISC') {
                    $discountExpenses += $amount;
                } else {
                    $regularExpenses += $amount;
                }
            }
        }

        $totalServiceRevenue = $cashSales + $creditSalesCash + $creditSalesReceivable;
        $totalVolume = $totalServiceRevenue + $receivablePayments + $regularExpenses + $refundExpenses + $discountExpenses;

        if ($totalVolume <= 0) {
            return null;
        }

        $description = "Closing {$closing->ct_number}";
        if ($closing->reception) {
            $description .= " — {$closing->reception->name}";
        }

        $incoming = AbacusIncoming::create([
            'source_type' => $closing->getMorphClass(),
            'source_id' => $closing->id,
            'reference' => $closing->ct_number,
            'date' => $closingDate->toDateString(),
            'description' => $description,
            'amount' => $totalVolume,
        ]);

        $dateStr = $closingDate->toDateString();
        $yearId = $yearModel->id;

        // 1. Cash Sales: Dr Cash, Cr Service Revenue
        if ($cashSales > 0) {
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $cashSales, $dateStr, 'debit');
            $this->createEntry($incoming->id, $serviceRevenue->id, $yearId, $cashSales, $dateStr, 'credit');
        }

        // 2. Credit Sales (cash portion): Dr Cash, Cr Service Revenue
        if ($creditSalesCash > 0) {
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $creditSalesCash, $dateStr, 'debit');
            $this->createEntry($incoming->id, $serviceRevenue->id, $yearId, $creditSalesCash, $dateStr, 'credit');
        }

        // 3. Credit Sales (receivable portion): Dr A/R, Cr Service Revenue
        if ($creditSalesReceivable > 0 && $accountsReceivable) {
            $this->createEntry($incoming->id, $accountsReceivable->id, $yearId, $creditSalesReceivable, $dateStr, 'debit');
            $this->createEntry($incoming->id, $serviceRevenue->id, $yearId, $creditSalesReceivable, $dateStr, 'credit');
        }

        // 4. Receivable Payments: Dr Cash, Cr A/R
        if ($receivablePayments > 0 && $accountsReceivable) {
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $receivablePayments, $dateStr, 'debit');
            $this->createEntry($incoming->id, $accountsReceivable->id, $yearId, $receivablePayments, $dateStr, 'credit');
        }

        // 5. Regular Expenses: Dr Other Expenses, Cr Cash
        if ($regularExpenses > 0) {
            $this->createEntry($incoming->id, $otherExpenses->id, $yearId, $regularExpenses, $dateStr, 'debit');
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $regularExpenses, $dateStr, 'credit');
        }

        // 6. Refunds: Dr Refunds, Cr Cash
        if ($refundExpenses > 0) {
            $refundAccount = $refundsAccount ?? $otherExpenses;
            $this->createEntry($incoming->id, $refundAccount->id, $yearId, $refundExpenses, $dateStr, 'debit');
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $refundExpenses, $dateStr, 'credit');
        }

        // 7. Discounts: Dr Discounts, Cr Cash
        if ($discountExpenses > 0) {
            $discountAccount = $discountsAccount ?? $otherExpenses;
            $this->createEntry($incoming->id, $discountAccount->id, $yearId, $discountExpenses, $dateStr, 'debit');
            $this->createEntry($incoming->id, $cashInHand->id, $yearId, $discountExpenses, $dateStr, 'credit');
        }

        return $incoming;
    }

    protected function createEntry(
        int $incomingId,
        int $accountId,
        int $yearId,
        float $amount,
        string $date,
        string $entryType
    ): AbacusTransaction {
        return AbacusTransaction::create([
            'abacus_incoming_id' => $incomingId,
            'abacus_chart_of_account_id' => $accountId,
            'abacus_year_id' => $yearId,
            'amount' => $amount,
            'date' => $date,
            'entry_type' => $entryType,
        ]);
    }

    protected function loadCoaCache(): void
    {
        if (! empty($this->coaCache)) {
            return;
        }

        AbacusChartOfAccount::where('is_group', false)->get()->each(function ($account) {
            $this->coaCache[$account->code] = $account;
        });
    }

    /**
     * Resolve or create a Pakistan financial year (July 1 – June 30) for a given date.
     */
    public function resolveOrCreateFinancialYear(Carbon $date): AbacusYear
    {
        $fyStartYear = $date->month >= 7 ? $date->year : $date->year - 1;
        $key = "FY_{$fyStartYear}";

        if (isset($this->yearCache[$key])) {
            return $this->yearCache[$key];
        }

        $fyStart = Carbon::create($fyStartYear, 7, 1)->startOfDay();
        $fyEnd = Carbon::create($fyStartYear + 1, 6, 30)->endOfDay();

        $year = AbacusYear::where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->first();

        if (! $year) {
            $year = AbacusYear::create([
                'start_date' => $fyStart,
                'end_date' => $fyEnd,
                'status' => 1,
            ]);
        }

        $this->yearCache[$key] = $year;

        return $year;
    }
}
