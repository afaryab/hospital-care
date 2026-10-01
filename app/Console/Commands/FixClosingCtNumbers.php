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

    protected $description = 'Reassign all CT numbers in closing-date order';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');

        return $this->reassignCtNumbers($dryRun, $chunk);
    }

    protected function reassignCtNumbers(bool $dryRun, int $chunk): int
    {
        $this->info('Loading all closings ordered by closing date...');

        // Sort by actual closing time so numbers are assigned in the order
        // the counter was physically closed, not when it was opened.
        $closings = Closing::query()
            ->orderByRaw('COALESCE(cash_recieving_time, created_at) ASC, id ASC')
            ->get(['id', 'ct_number', 'cash_recieving_time', 'created_at']);

        $total = $closings->count();
        $this->info("Reviewing {$total} closings...");

        // Build new CT numbers entirely in memory — one counter per Y/m key,
        // zero DB round-trips per closing.
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
            // Phase A — stamp temp IDs to avoid unique-key conflicts mid-flight.
            foreach (array_chunk(array_keys($updates), $chunk) as $ids) {
                DB::table('closings')
                    ->whereIn('id', $ids)
                    ->update(['ct_number' => DB::raw("CONCAT('TMP/', id)")]);
            }

            // Phase B — write the correct CT numbers.
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
