<?php

namespace App\Console\Commands;

use App\Services\VersionSnapshotEncryption;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class EncryptVersionSnapshots extends Command
{
    protected $signature = 'versions:encrypt-snapshots
        {--table=* : Only these version tables (default: all three)}
        {--batch=1000 : Rows per batch; each batch is one commit}
        {--dry-run : Count plaintext snapshots without changing anything}';

    protected $description = 'Encrypt legacy plaintext version snapshots (resumable; safe to run while the app is live)';

    public function handle(VersionSnapshotEncryption $encryption): int
    {
        $tables = $this->option('table') ?: VersionSnapshotEncryption::TABLES;
        $unknown = array_diff($tables, VersionSnapshotEncryption::TABLES);

        if ($unknown !== []) {
            $this->error('Unknown table(s): '.implode(', ', $unknown).'. Allowed: '.implode(', ', VersionSnapshotEncryption::TABLES));

            return self::FAILURE;
        }

        $batchSize = max(1, (int) $this->option('batch'));
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN — no changes will be made.');
        }

        foreach ($tables as $table) {
            if (Schema::getColumnType($table, 'snapshot') === 'json') {
                $this->warn("Skipping {$table}: snapshot is still a json column — run php artisan migrate first.");

                continue;
            }

            $this->info("Processing {$table}…");

            $result = $encryption->encryptLegacy(
                $table,
                $batchSize,
                $dryRun,
                function (int $scanned, int $encrypted): void {
                    $this->output->write("\r  scanned {$scanned} · ".($this->option('dry-run') ? 'plaintext' : 'encrypted')." {$encrypted}");
                },
            );

            $this->newLine();
            $this->info(sprintf(
                '  %s: %d of %d rows %s.',
                $table,
                $result['encrypted'],
                $result['scanned'],
                $dryRun ? 'are still plaintext' : 'encrypted',
            ));
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
