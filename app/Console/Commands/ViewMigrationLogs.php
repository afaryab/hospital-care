<?php

namespace App\Console\Commands;

use App\Models\MigrationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ViewMigrationLogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:view-migration-logs 
                            {--step= : Filter by migration step}
                            {--action= : Filter by action type (success, error, skipped, duplicated)}
                            {--batch= : Filter by batch ID}
                            {--table= : Filter by old table name}
                            {--limit=50 : Limit number of results}
                            {--summary : Show summary statistics}
                            {--financial : Show financial tracking logs}
                            {--errors-only : Show only errors and warnings}';

    /**
     * The console command description.
     */
    protected $description = 'View migration logs and analyze migration issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('summary')) {
            $this->showSummary();

            return;
        }

        if ($this->option('financial')) {
            $this->showFinancialLogs();

            return;
        }

        $this->showDetailedLogs();
    }

    /**
     * Show summary statistics
     */
    protected function showSummary()
    {
        $this->info('📊 Migration Logs Summary');
        $this->line('');

        // Overall statistics
        $totalLogs = MigrationLog::count();
        $this->line("Total log entries: {$totalLogs}");

        // By action type
        $actionStats = MigrationLog::select('action_type', DB::raw('count(*) as count'))
            ->groupBy('action_type')
            ->get();

        $this->line('');
        $this->info('📈 By Action Type:');
        foreach ($actionStats as $stat) {
            $icon = $this->getActionIcon($stat->action_type);
            $this->line("  {$icon} {$stat->action_type}: {$stat->count}");
        }

        // By migration step
        $stepStats = MigrationLog::select('migration_step', DB::raw('count(*) as count'))
            ->groupBy('migration_step')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $this->line('');
        $this->info('📋 Top Migration Steps (by log count):');
        foreach ($stepStats as $stat) {
            $this->line("  • {$stat->migration_step}: {$stat->count} entries");
        }

        // Recent batches
        $recentBatches = MigrationLog::select('batch_id', DB::raw('count(*) as count'), DB::raw('max(created_at) as last_run'))
            ->whereNotNull('batch_id')
            ->groupBy('batch_id')
            ->orderBy('last_run', 'desc')
            ->limit(5)
            ->get();

        $this->line('');
        $this->info('🕒 Recent Migration Batches:');
        foreach ($recentBatches as $batch) {
            $this->line("  • {$batch->batch_id}: {$batch->count} entries (Last: {$batch->last_run})");
        }

        // Error analysis
        $errorCount = MigrationLog::where('action_type', 'error')->count();
        $skippedCount = MigrationLog::where('action_type', 'skipped')->count();
        $duplicatedCount = MigrationLog::where('action_type', 'duplicated')->count();

        if ($errorCount > 0 || $skippedCount > 0 || $duplicatedCount > 0) {
            $this->line('');
            $this->info('⚠️  Issues Found:');
            if ($errorCount > 0) {
                $this->line("  • Errors: {$errorCount}");
            }
            if ($skippedCount > 0) {
                $this->line("  • Skipped records: {$skippedCount}");
            }
            if ($duplicatedCount > 0) {
                $this->line("  • Duplicates detected: {$duplicatedCount}");
            }
        }
    }

    /**
     * Show financial tracking logs
     */
    protected function showFinancialLogs()
    {
        $this->info('💰 Financial Migration Tracking');
        $this->line('');

        $financialLogs = MigrationLog::whereNotNull('old_amount')
            ->whereNotNull('new_amount')
            ->select('migration_step', 'old_table',
                DB::raw('count(*) as count'),
                DB::raw('sum(old_amount) as total_old_amount'),
                DB::raw('sum(new_amount) as total_new_amount'),
                DB::raw('sum(abs(old_amount - new_amount)) as total_variance')
            )
            ->groupBy('migration_step', 'old_table')
            ->get();

        foreach ($financialLogs as $log) {
            $variance = $log->total_old_amount > 0 ?
                (abs($log->total_old_amount - $log->total_new_amount) / $log->total_old_amount) * 100 : 0;

            $this->line("📊 {$log->migration_step} ({$log->old_table}):");
            $this->line("   • Records: {$log->count}");
            $this->line('   • Old Amount: '.number_format($log->total_old_amount, 2));
            $this->line('   • New Amount: '.number_format($log->total_new_amount, 2));
            $this->line('   • Variance: '.number_format($variance, 2).'%');
            $this->line('');
        }
    }

    /**
     * Show detailed logs
     */
    protected function showDetailedLogs()
    {
        $query = MigrationLog::query()->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->option('step')) {
            $query->where('migration_step', $this->option('step'));
        }

        if ($this->option('action')) {
            $query->where('action_type', $this->option('action'));
        }

        if ($this->option('batch')) {
            $query->where('batch_id', 'like', '%'.$this->option('batch').'%');
        }

        if ($this->option('table')) {
            $query->where('old_table', $this->option('table'));
        }

        if ($this->option('errors-only')) {
            $query->whereIn('action_type', ['error', 'skipped', 'duplicated', 'validation_failed']);
        }

        $logs = $query->limit($this->option('limit'))->get();

        $this->info('📋 Migration Logs ('.$logs->count().' entries)');
        $this->line('');

        foreach ($logs as $log) {
            $icon = $this->getActionIcon($log->action_type);
            $this->line("{$icon} [{$log->created_at}] {$log->migration_step} - {$log->action_type}");

            if ($log->old_table && $log->old_record_id) {
                $this->line("   Source: {$log->old_table}#{$log->old_record_id}");
            }

            if ($log->new_table && $log->new_record_id) {
                $this->line("   Target: {$log->new_table}#{$log->new_record_id}");
            }

            if ($log->reason) {
                $this->line("   Reason: {$log->reason}");
            }

            if ($log->old_amount || $log->new_amount) {
                $this->line('   Amounts: '.($log->old_amount ? number_format($log->old_amount, 2) : 'N/A').
                          ' → '.($log->new_amount ? number_format($log->new_amount, 2) : 'N/A'));
            }

            if ($log->error_details) {
                $this->line('   Error: '.substr($log->error_details, 0, 100).'...');
            }

            $this->line('');
        }

        // Show available filters
        if (! $this->option('step') && ! $this->option('action')) {
            $this->line('');
            $this->info('💡 Available filters:');
            $this->line('   --step=users          Filter by migration step');
            $this->line('   --action=error        Filter by action type');
            $this->line('   --table=expenses      Filter by source table');
            $this->line('   --errors-only         Show only problems');
            $this->line('   --financial           Show financial tracking');
            $this->line('   --summary             Show summary statistics');
        }
    }

    /**
     * Get icon for action type
     */
    protected function getActionIcon($actionType)
    {
        return match ($actionType) {
            'success' => '✅',
            'error' => '❌',
            'skipped' => '⏭️',
            'duplicated' => '🔄',
            'warning' => '⚠️',
            'validation_failed' => '❗',
            'sanitized' => '🧹',
            default => '📝'
        };
    }
}
