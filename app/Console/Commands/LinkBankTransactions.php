<?php

namespace App\Console\Commands;

use App\Models\BankTransaction;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LinkBankTransactions extends Command
{
    protected $signature = 'bank:link-transactions
                            {--dry-run : Show matches without saving}';

    protected $description = 'Auto-link bank statement rows to hospital transactions by amount and reference number';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $linked = 0;
        $skipped = 0;

        BankTransaction::query()
            ->whereNull('linked_transaction_id')
            ->whereNotNull('reference_number')
            ->whereNotNull('credit')
            ->chunkById(200, function ($bankTxns) use (&$linked, &$skipped, $dryRun): void {
                foreach ($bankTxns as $bt) {
                    // Match by amount and reference number contained anywhere in transaction reference_number or notes
                    $match = Transaction::query()
                        ->where('amount', $bt->credit)
                        ->where(function ($q) use ($bt): void {
                            $q->where('reference_number', 'like', "%{$bt->reference_number}%")
                                ->orWhere('notes', 'like', "%{$bt->reference_number}%");
                        })
                        ->first();

                    if (! $match) {
                        $skipped++;

                        continue;
                    }

                    if ($dryRun) {
                        $this->line("MATCH: BankTxn #{$bt->id} (ref={$bt->reference_number}, PKR {$bt->credit}) → TR {$match->tr_number}");
                        $linked++;

                        continue;
                    }

                    DB::transaction(function () use ($bt, $match): void {
                        $bt->update(['linked_transaction_id' => $match->id]);
                    });

                    $linked++;
                }
            });

        $this->info("{$linked} bank transactions linked, {$skipped} could not be matched.");

        return self::SUCCESS;
    }
}
