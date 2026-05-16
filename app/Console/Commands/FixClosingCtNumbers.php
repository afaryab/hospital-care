<?php

namespace App\Console\Commands;

use App\Models\Closing;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixClosingCtNumbers extends Command
{
    protected $signature = 'app:fix-closing-ct-numbers
        {--dry-run : Preview what would change without writing to DB}
        {--chunk=500 : Number of rows per update batch}';

    protected $description = 'Reassign CT numbers for all closings using cash_recieving_time as the closing date';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');

        $this->info('Loading all closings ordered by closing date...');

        // Load every closing sorted by when the counter was actually closed,
        // falling back to created_at for records with no cash_recieving_time.
        $closings = Closing::query()
            ->orderByRaw('COALESCE(cash_recieving_time, created_at) ASC, id ASC')
            ->get(['id', 'ct_number', 'cash_recieving_time', 'created_at']);

        $total = $closings->count();
        $this->info("Reviewing {$total} closings...");

        // Build new CT numbers entirely in memory — O(n) counter increments,
        // no DB round-trip per closing.
        $counters = [];
        $updates = [];      // [id => ['old' => ..., 'new' => ...]]
        $monthSummary = []; // [Y/m => change count]

        foreach ($closings as $closing) {
            $date = Carbon::parse($closing->cash_recieving_time ?? $closing->created_at);
            $key = $date->format('Y/m');

            $counters[$key] = ($counters[$key] ?? 0) + 1;
            $newCtNumber = 'CT/'.$key.'/'.str_pad($counters[$key], 4, '0', STR_PAD_LEFT);

            if ($newCtNumber !== $closing->ct_number) {
                $updates[$closing->id] = ['old' => $closing->ct_number, 'new' => $newCtNumber];
                $monthSummary[$key] = ($monthSummary[$key] ?? 0) + 1;
            }
        }

        $changeCount = count($updates);

        if ($changeCount === 0) {
            $this->info('All CT numbers are already correct. Nothing to update.');

            return 0;
        }

        // Month-by-month summary
        $this->newLine();
        $this->info("Found {$changeCount} closings that need correction:");
        $this->table(
            ['Month', 'Changes'],
            collect($monthSummary)
                ->sortKeys()
                ->map(fn ($count, $month) => [$month, $count])
                ->values()
                ->toArray()
        );

        if ($dryRun) {
            $this->newLine();
            $preview = collect($updates)
                ->take(20)
                ->map(fn ($u, $id) => [$id, $u['old'], $u['new']])
                ->values()
                ->toArray();

            $this->table(['Closing ID', 'Current CT Number', 'Corrected CT Number'], $preview);

            if ($changeCount > 20) {
                $this->line('  ... and '.($changeCount - 20).' more.');
            }

            $this->warn('[DRY RUN] No changes written.');

            return 0;
        }

        $this->info('Applying corrections...');

        DB::transaction(function () use ($updates, $chunk) {
            // Phase 1 — stamp a unique temp prefix so the reassignment cannot
            // hit a duplicate-key conflict mid-flight.
            foreach (array_chunk(array_keys($updates), $chunk) as $ids) {
                DB::table('closings')
                    ->whereIn('id', $ids)
                    ->update(['ct_number' => DB::raw("CONCAT('TMP/', id)")]);
            }

            // Phase 2 — write the correct CT numbers.
            foreach (array_chunk($updates, $chunk, true) as $batch) {
                foreach ($batch as $id => $change) {
                    DB::table('closings')
                        ->where('id', $id)
                        ->update(['ct_number' => $change['new']]);
                }
            }
        });

        $msg = "Fixed {$changeCount} CT numbers across ".count($monthSummary).' months.';
        $this->info($msg);
        Log::info("[FixClosingCtNumbers] {$msg}");

        return 0;
    }
}
