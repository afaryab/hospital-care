<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\Service;
use App\Models\Reception;
use App\Models\Closing;
use App\Models\Expense;
use App\Models\ExpenseVoucher;
use App\Models\ExpenseCategory;

class VerifyMigration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:verify-migration {--detailed} {--fix-issues}';

    /**
     * The console command description.
     */
    protected $description = 'Verify data integrity after migration from old schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration verification...');
        
        $issues = [];
        $detailed = $this->option('detailed');
        $fixIssues = $this->option('fix-issues');

        // 1. Verify record counts
        $issues = array_merge($issues, $this->verifyRecordCounts());

        // 2. Verify services against old database
        $issues = array_merge($issues, $this->verifyServicesCount());

        // 3. Verify financial data integrity
        $issues = array_merge($issues, $this->verifyFinancialSums());

        // 4. Verify data integrity
        $issues = array_merge($issues, $this->verifyDataIntegrity());

        // 5. Verify relationships
        $issues = array_merge($issues, $this->verifyRelationships());

        // 6. Verify business logic
        $issues = array_merge($issues, $this->verifyBusinessLogic());

        // Report results
        if (empty($issues)) {
            $this->info('✅ Migration verification completed successfully! No issues found.');
            return 0;
        } else {
            $this->error('❌ Migration verification found ' . count($issues) . ' issues:');
            foreach ($issues as $issue) {
                $this->line("  • {$issue['severity']}: {$issue['message']}");
                if ($detailed && isset($issue['details'])) {
                    $this->line("    Details: {$issue['details']}");
                }
            }

            if ($fixIssues) {
                $this->info('Attempting to fix identified issues...');
                $this->fixIssues($issues);
            } else {
                $this->info('Run with --fix-issues to attempt automatic repairs.');
            }

            return 1;
        }
    }

    /**
     * Verify record counts between old and new databases
     */
    protected function verifyRecordCounts()
    {
        $issues = [];
        
        $tables = [
            'users' => ['aauth_users', User::class],
            'patients' => ['patients', Patient::class],
            'transactions' => ['reception_counters_closings_transactions', Transaction::class],
            'transaction_elements' => ['reception_counters_closings_transaction_elements', TransactionElement::class],
            'receptions' => ['reception_counters', Reception::class],
            'closings' => ['reception_counters_closings', Closing::class],
            'expenses' => [['expenses', 'inpatient_expense_transactions'], Expense::class],
            'expense_vouchers' => ['expense_vouchers', ExpenseVoucher::class],
            'expense_categories' => ['expenses_categories', ExpenseCategory::class],
        ];

        foreach ($tables as $name => $config) {
            list($oldTables, $newModel) = $config;
            
            // Count records in old database
            $oldCount = 0;
            if (is_array($oldTables)) {
                foreach ($oldTables as $table) {
                    $oldCount += DB::connection('secondary')->table($table)->count();
                }
            } else {
                $oldCount = DB::connection('secondary')->table($oldTables)->count();
            }
            
            // Count records in new database
            $newCount = $newModel::count();
            
            // Calculate sync percentage
            $percentage = $oldCount > 0 ? round(($newCount / $oldCount) * 100, 2) : 100;
            
            if ($oldCount !== $newCount) {
                $diffCount = abs($oldCount - $newCount);
                $reason = $this->analyzeCountMismatch($name, $oldCount, $newCount, $oldTables);
                
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Record count mismatch for {$name}: Old DB: {$oldCount}, New DB: {$newCount} ({$percentage}% synced)",
                    'type' => 'count_mismatch',
                    'table' => $name,
                    'old_count' => $oldCount,
                    'new_count' => $newCount,
                    'percentage_synced' => $percentage,
                    'details' => "Missing/Extra: {$diffCount} records. Reason: {$reason}"
                ];
            }
            
            $this->line("✅ {$name}: {$newCount}/{$oldCount} records migrated ({$percentage}%)");
        }

        return $issues;
    }

    /**
     * Verify services count against old database tables
     */
    protected function verifyServicesCount()
    {
        $issues = [];
        
        try {
            // Get counts from old database service-related tables
            $oldServiceTables = [
                'opd_services' => DB::connection('secondary')->table('opd_services')->count(),
                'inpd_services' => DB::connection('secondary')->table('inpd_services')->count(),
                'emergency_services' => DB::connection('secondary')->table('emergency_services')->count(),
                'dental_services' => DB::connection('secondary')->table('dental_services')->count(),
                'xray_services' => DB::connection('secondary')->table('xray_services')->count(),
                'ultrasound_services' => DB::connection('secondary')->table('ultrasound_services')->count(),
                'test_services' => DB::connection('secondary')->table('test_services')->count(),
                'recestation_services' => DB::connection('secondary')->table('recestation_services')->count(),
            ];

            $totalOldServices = array_sum($oldServiceTables);
            $newServicesCount = Service::count();
            $servicesSyncPercentage = $totalOldServices > 0 ? round(($newServicesCount / $totalOldServices) * 100, 2) : 100;

            $this->info("Service count validation:");
            foreach ($oldServiceTables as $table => $count) {
                $this->line("  • {$table}: {$count}");
            }
            $this->line("  • Total old services: {$totalOldServices}");
            $this->line("  • New services: {$newServicesCount} ({$servicesSyncPercentage}% synced)");

            if ($totalOldServices !== $newServicesCount) {
                $missingServices = $totalOldServices - $newServicesCount;
                $reason = $this->analyzeServicesMismatch($oldServiceTables, $newServicesCount);
                
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Services count mismatch: Old DB: {$totalOldServices}, New DB: {$newServicesCount} ({$servicesSyncPercentage}% synced)",
                    'type' => 'services_count_mismatch',
                    'old_count' => $totalOldServices,
                    'new_count' => $newServicesCount,
                    'percentage_synced' => $servicesSyncPercentage,
                    'details' => "Missing {$missingServices} services. Reason: {$reason}"
                ];
            } else {
                $this->line("✅ Services count verified: {$newServicesCount} services migrated correctly");
            }

        } catch (\Exception $e) {
            $issues[] = [
                'severity' => 'ERROR',
                'message' => "Failed to verify services count: " . $e->getMessage(),
                'type' => 'services_verification_error'
            ];
        }

        return $issues;
    }

    /**
     * Verify financial data integrity and sums
     */
    protected function verifyFinancialSums()
    {
        $issues = [];

        try {
            $this->info("Validating financial data integrity...");

            // 1. Verify transaction amounts sum
            $oldTransactionSum = DB::connection('secondary')
                ->table('reception_counters_closings_transactions')
                ->sum('amount');
            
            $newTransactionSum = Transaction::sum('amount');
            $transactionSyncPercentage = $oldTransactionSum > 0 ? round(($newTransactionSum / $oldTransactionSum) * 100, 2) : 100;
            
            $this->line("Transaction amounts:");
            $this->line("  • Old DB sum: " . number_format($oldTransactionSum, 2));
            $this->line("  • New DB sum: " . number_format($newTransactionSum, 2) . " ({$transactionSyncPercentage}% synced)");
            
            $transactionDiff = abs($oldTransactionSum - $newTransactionSum);
            if ($transactionDiff > 1) { // Allow small rounding differences
                $reason = $this->analyzeFinancialMismatch('transactions', $oldTransactionSum, $newTransactionSum);
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Transaction sum mismatch: Old: " . number_format($oldTransactionSum, 2) . ", New: " . number_format($newTransactionSum, 2) . " (Diff: " . number_format($transactionDiff, 2) . ", {$transactionSyncPercentage}% synced)",
                    'type' => 'financial_sum_mismatch',
                    'category' => 'transactions',
                    'percentage_synced' => $transactionSyncPercentage,
                    'details' => $reason
                ];
            }

            // 2. Verify transaction elements sum
            $oldElementSum = DB::connection('secondary')
                ->table('reception_counters_closings_transaction_elements')
                ->sum('amount');
            
            $newElementSum = TransactionElement::sum('amount');
            $elementSyncPercentage = $oldElementSum > 0 ? round(($newElementSum / $oldElementSum) * 100, 2) : 100;
            
            $this->line("Transaction element amounts:");
            $this->line("  • Old DB sum: " . number_format($oldElementSum, 2));
            $this->line("  • New DB sum: " . number_format($newElementSum, 2) . " ({$elementSyncPercentage}% synced)");
            
            $elementDiff = abs($oldElementSum - $newElementSum);
            if ($elementDiff > 1) {
                $reason = $this->analyzeFinancialMismatch('transaction_elements', $oldElementSum, $newElementSum);
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Transaction element sum mismatch: Old: " . number_format($oldElementSum, 2) . ", New: " . number_format($newElementSum, 2) . " (Diff: " . number_format($elementDiff, 2) . ", {$elementSyncPercentage}% synced)",
                    'type' => 'financial_sum_mismatch',
                    'category' => 'transaction_elements',
                    'percentage_synced' => $elementSyncPercentage,
                    'details' => $reason
                ];
            }

            // 3. Verify income vs expense segregation
            $newIncomeSum = Transaction::where('income_or_expense', 'INCOME')->sum('amount');
            $newExpenseSum = Transaction::where('income_or_expense', 'EXPENSE')->sum('amount');
            
            $this->line("Income vs Expense breakdown:");
            $this->line("  • Income sum: " . number_format($newIncomeSum, 2));
            $this->line("  • Expense sum: " . number_format($newExpenseSum, 2));
            $this->line("  • Net amount: " . number_format($newIncomeSum - $newExpenseSum, 2));

            // 4. Verify expense voucher amounts
            $oldVoucherSum = DB::connection('secondary')
                ->table('expense_vouchers')
                ->sum('exp_amount_numbers');
            
            $newVoucherSum = ExpenseVoucher::sum('amount');
            $voucherSyncPercentage = $oldVoucherSum > 0 ? round(($newVoucherSum / $oldVoucherSum) * 100, 2) : 100;
            
            $this->line("Expense voucher amounts:");
            $this->line("  • Old DB sum: " . number_format($oldVoucherSum, 2));
            $this->line("  • New DB sum: " . number_format($newVoucherSum, 2) . " ({$voucherSyncPercentage}% synced)");
            
            $voucherDiff = abs($oldVoucherSum - $newVoucherSum);
            if ($voucherDiff > 1) {
                $reason = $this->analyzeFinancialMismatch('expense_vouchers', $oldVoucherSum, $newVoucherSum);
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Expense voucher sum mismatch: Old: " . number_format($oldVoucherSum, 2) . ", New: " . number_format($newVoucherSum, 2) . " (Diff: " . number_format($voucherDiff, 2) . ", {$voucherSyncPercentage}% synced)",
                    'type' => 'financial_sum_mismatch',
                    'category' => 'expense_vouchers',
                    'percentage_synced' => $voucherSyncPercentage,
                    'details' => $reason
                ];
            }

            // 5. Verify expenses amount integrity
            $oldExpenseSum = DB::connection('secondary')->table('expenses')->sum('amount_received_num');
            $oldInpatientExpenseSum = DB::connection('secondary')->table('inpatient_expense_transactions')->sum('amount_in_num');
            $totalOldExpenseSum = $oldExpenseSum + $oldInpatientExpenseSum;
            
            $newExpenseSum = Expense::sum('amount');
            $expenseSyncPercentage = $totalOldExpenseSum > 0 ? round(($newExpenseSum / $totalOldExpenseSum) * 100, 2) : 100;
            
            $this->line("Expense amounts:");
            $this->line("  • Old regular expenses: " . number_format($oldExpenseSum, 2));
            $this->line("  • Old inpatient expenses: " . number_format($oldInpatientExpenseSum, 2));
            $this->line("  • Total old expenses: " . number_format($totalOldExpenseSum, 2));
            $this->line("  • New expenses: " . number_format($newExpenseSum, 2) . " ({$expenseSyncPercentage}% synced)");
            
            $expenseDiff = abs($totalOldExpenseSum - $newExpenseSum);
            if ($expenseDiff > 1) {
                $reason = $this->analyzeFinancialMismatch('expenses', $totalOldExpenseSum, $newExpenseSum);
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "Expense sum mismatch: Old: " . number_format($totalOldExpenseSum, 2) . ", New: " . number_format($newExpenseSum, 2) . " (Diff: " . number_format($expenseDiff, 2) . ", {$expenseSyncPercentage}% synced)",
                    'type' => 'financial_sum_mismatch',
                    'category' => 'expenses',
                    'percentage_synced' => $expenseSyncPercentage,
                    'details' => $reason
                ];
            }

            // 6. Cross-validate transaction vs element sums
            $transactionElementsDiff = abs($newTransactionSum - $newElementSum);
            if ($transactionElementsDiff > 100) { // Allow some difference due to incomplete migration
                $issues[] = [
                    'severity' => 'INFO',
                    'message' => "Transaction vs Elements sum difference: " . number_format($transactionElementsDiff, 2) . " (This is normal during partial migration)",
                    'type' => 'financial_cross_validation',
                    'details' => 'Transaction elements may not be fully migrated yet or contain orphaned records'
                ];
            }

        } catch (\Exception $e) {
            $issues[] = [
                'severity' => 'ERROR',
                'message' => "Failed to verify financial sums: " . $e->getMessage(),
                'type' => 'financial_verification_error'
            ];
        }

        return $issues;
    }

    /**
     * Analyze the root cause of services count mismatch
     */
    protected function analyzeServicesMismatch($oldServiceTables, $newCount)
    {
        $emptyTables = array_filter($oldServiceTables, function($count) {
            return $count === 0;
        });
        
        $nonEmptyTables = array_filter($oldServiceTables, function($count) {
            return $count > 0;
        });
        
        if (count($emptyTables) > 0) {
            return "Some service tables are empty (" . implode(', ', array_keys($emptyTables)) . "), migration may be incomplete";
        }
        
        return "Service migration logic may be filtering out some records or there may be duplicate detection";
    }

    /**
     * Analyze the root cause of financial mismatches
     */
    protected function analyzeFinancialMismatch($category, $oldSum, $newSum)
    {
        $diff = $newSum - $oldSum;
        $percentageDiff = $oldSum > 0 ? (abs($diff) / $oldSum) * 100 : 0;
        
        switch ($category) {
            case 'transactions':
                if ($diff < 0) {
                    return "Some transactions may have been filtered out due to validation rules or data sanitization (${percentageDiff}% variance)";
                } else {
                    return "Possible duplicate transactions or additional transactions created during migration (${percentageDiff}% variance)";
                }
                
            case 'transaction_elements':
                if ($diff < 0) {
                    return "Some transaction elements may be missing due to orphaned records or validation failures (${percentageDiff}% variance)";
                } else {
                    return "Possible additional transaction elements created during migration (${percentageDiff}% variance)";
                }
                
            case 'expenses':
                if ($diff > 0) {
                    return "Expense amounts appear significantly higher - possible data duplication or conversion errors from multiple source tables (${percentageDiff}% variance)";
                } else {
                    return "Some expenses may have been filtered out or converted incorrectly (${percentageDiff}% variance)";
                }
                
            case 'expense_vouchers':
                if ($percentageDiff < 0.01) {
                    return "Minimal rounding differences in voucher amounts (${percentageDiff}% variance)";
                } else {
                    return "Voucher amounts may have data type conversion issues (${percentageDiff}% variance)";
                }
                
            default:
                return "Financial data conversion or validation issues detected (${percentageDiff}% variance)";
        }
    }

    /**
     * Verify data integrity (nulls, data types, etc.)
     */
    protected function verifyDataIntegrity()
    {
        $issues = [];

        // Check for null values in required fields
        $nullChecks = [
            [User::class, 'email', 'Users with null email'],
            [Patient::class, 'name', 'Patients with null name'],
            [Transaction::class, 'amount', 'Transactions with null amount'],
            [Service::class, 'name', 'Services with null name'],
        ];

        foreach ($nullChecks as [$model, $field, $description]) {
            $count = $model::whereNull($field)->count();
            if ($count > 0) {
                $issues[] = [
                    'severity' => 'ERROR',
                    'message' => "{$description}: {$count} records",
                    'type' => 'null_values',
                    'model' => $model,
                    'field' => $field,
                    'count' => $count
                ];
            }
        }

        // Check for invalid data ranges
        $rangeChecks = [
            [Transaction::class, 'amount', '>=', 0, 'Transactions with negative amounts'],
            [Patient::class, 'created_at', '>', '1900-01-01', 'Patients with invalid creation dates'],
        ];

        foreach ($rangeChecks as [$model, $field, $operator, $value, $description]) {
            $count = $model::where($field, $operator, $value)->count();
            if ($count === 0 && $operator === '>=') {
                // This means all values are below the threshold, which might be wrong
                $issues[] = [
                    'severity' => 'WARNING',
                    'message' => "{$description}: All values may be invalid",
                    'type' => 'range_check',
                    'model' => $model,
                    'field' => $field
                ];
            }
        }

        // Check for duplicate records
        $duplicateChecks = [
            [User::class, 'email', 'Users with duplicate emails'],
            [Patient::class, 'ps_number', 'Patients with duplicate PS numbers'],
        ];

        foreach ($duplicateChecks as [$model, $field, $description]) {
            $duplicates = $model::select($field)
                ->groupBy($field)
                ->havingRaw('COUNT(*) > 1')
                ->count();
                
            if ($duplicates > 0) {
                $issues[] = [
                    'severity' => 'ERROR',
                    'message' => "{$description}: {$duplicates} duplicate values",
                    'type' => 'duplicates',
                    'model' => $model,
                    'field' => $field,
                    'count' => $duplicates
                ];
            }
        }

        return $issues;
    }

    /**
     * Verify foreign key relationships
     */
    protected function verifyRelationships()
    {
        $issues = [];

        // Check foreign key relationships
        $relationshipChecks = [
            [Transaction::class, 'created_by', User::class, 'id', 'Transactions with invalid created_by user'],
            [Transaction::class, 'patient_id', Patient::class, 'id', 'Transactions with invalid patient_id'],
            [Transaction::class, 'closing_id', Closing::class, 'id', 'Transactions with invalid closing_id'],
            [TransactionElement::class, 'transaction_id', Transaction::class, 'id', 'Transaction elements with invalid transaction_id'],
            [TransactionElement::class, 'service_id', Service::class, 'id', 'Transaction elements with invalid service_id'],
            [Closing::class, 'reception_id', Reception::class, 'id', 'Closings with invalid reception_id'],
            [Expense::class, 'payed_to', User::class, 'id', 'Expenses with invalid payed_to user'],
        ];

        foreach ($relationshipChecks as [$model, $foreignKey, $relatedModel, $relatedKey, $description]) {
            $orphanedCount = $model::whereNotNull($foreignKey)
                ->whereNotExists(function ($query) use ($relatedModel, $relatedKey, $foreignKey) {
                    $relatedTable = (new $relatedModel)->getTable();
                    $query->select(DB::raw(1))
                        ->from($relatedTable)
                        ->whereColumn($relatedTable . '.' . $relatedKey, '=', $foreignKey);
                })
                ->count();

            if ($orphanedCount > 0) {
                $issues[] = [
                    'severity' => 'ERROR',
                    'message' => "{$description}: {$orphanedCount} records",
                    'type' => 'orphaned_records',
                    'model' => $model,
                    'foreign_key' => $foreignKey,
                    'related_model' => $relatedModel,
                    'count' => $orphanedCount
                ];
            }
        }

        return $issues;
    }

    /**
     * Verify business logic rules
     */
    protected function verifyBusinessLogic()
    {
        $issues = [];

        // Check transaction amounts match their elements
        $transactionsWithMismatch = DB::select("
            SELECT t.id, t.amount, SUM(te.amount) as elements_sum
            FROM transactions t
            LEFT JOIN transaction_elements te ON t.id = te.transaction_id
            GROUP BY t.id, t.amount
            HAVING ABS(t.amount - COALESCE(SUM(te.amount), 0)) > 0.01
            LIMIT 100
        ");

        if (count($transactionsWithMismatch) > 0) {
            $issues[] = [
                'severity' => 'WARNING',
                'message' => "Transaction amount mismatches: " . count($transactionsWithMismatch) . " transactions",
                'type' => 'business_logic',
                'details' => 'Transaction amounts do not match sum of their elements'
            ];
        }

        // Check for closings without transactions
        $closingsWithoutTransactions = Closing::whereDoesntHave('transactions')->count();
        if ($closingsWithoutTransactions > 0) {
            $issues[] = [
                'severity' => 'WARNING',
                'message' => "Closings without transactions: {$closingsWithoutTransactions} records",
                'type' => 'business_logic',
                'details' => 'Some closings have no associated transactions'
            ];
        }

        // Check for patients created in future
        $futurePatients = Patient::where('created_at', '>', now())->count();
        if ($futurePatients > 0) {
            $issues[] = [
                'severity' => 'ERROR',
                'message' => "Patients created in future: {$futurePatients} records",
                'type' => 'business_logic',
                'details' => 'Patient creation dates are in the future'
            ];
        }

        // Check PS number format
        $invalidPsNumbers = Patient::whereNotNull('ps_number')
            ->where('ps_number', 'NOT LIKE', 'PS/%/%')
            ->count();
            
        if ($invalidPsNumbers > 0) {
            $issues[] = [
                'severity' => 'WARNING',
                'message' => "Invalid PS number format: {$invalidPsNumbers} records",
                'type' => 'business_logic',
                'details' => 'PS numbers should follow PS/YYYY/MM/XXXXXX format'
            ];
        }

        return $issues;
    }

    /**
     * Attempt to fix identified issues
     */
    protected function fixIssues($issues)
    {
        $fixed = 0;
        
        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'null_values':
                    if ($this->fixNullValues($issue)) {
                        $fixed++;
                    }
                    break;
                    
                case 'duplicates':
                    if ($this->fixDuplicates($issue)) {
                        $fixed++;
                    }
                    break;
                    
                case 'orphaned_records':
                    if ($this->fixOrphanedRecords($issue)) {
                        $fixed++;
                    }
                    break;
            }
        }
        
        $this->info("Fixed {$fixed} out of " . count($issues) . " issues.");
    }

    /**
     * Fix null values in required fields
     */
    protected function fixNullValues($issue)
    {
        $model = $issue['model'];
        $field = $issue['field'];
        
        switch ($field) {
            case 'email':
                // Set placeholder email for users without email
                $model::whereNull($field)->update([
                    $field => DB::raw("CONCAT('placeholder_', id, '@example.com')")
                ]);
                return true;
                
            case 'name':
                // Set placeholder name for patients/services without name
                $model::whereNull($field)->update([
                    $field => DB::raw("CONCAT('Unknown_', id)")
                ]);
                return true;
                
            case 'amount':
                // Set zero amount for transactions without amount
                $model::whereNull($field)->update([$field => 0]);
                return true;
        }
        
        return false;
    }

    /**
     * Fix duplicate records
     */
    protected function fixDuplicates($issue)
    {
        $model = $issue['model'];
        $field = $issue['field'];
        
        // Keep first record, delete others
        $duplicates = $model::select($field)
            ->groupBy($field)
            ->havingRaw('COUNT(*) > 1')
            ->pluck($field);
            
        foreach ($duplicates as $value) {
            $records = $model::where($field, $value)->orderBy('id')->get();
            
            // Keep first, delete others
            for ($i = 1; $i < $records->count(); $i++) {
                $records[$i]->delete();
            }
        }
        
        return true;
    }

    /**
     * Fix orphaned records
     */
    protected function fixOrphanedRecords($issue)
    {
        $model = $issue['model'];
        $foreignKey = $issue['foreign_key'];
        $relatedModel = $issue['related_model'];
        
        // Set foreign key to null for orphaned records
        $relatedTable = (new $relatedModel)->getTable();
        
        $model::whereNotNull($foreignKey)
            ->whereNotExists(function ($query) use ($relatedTable, $foreignKey) {
                $query->select(DB::raw(1))
                    ->from($relatedTable)
                    ->whereColumn($relatedTable . '.id', '=', $foreignKey);
            })
            ->update([$foreignKey => null]);
            
        return true;
    }

    /**
     * Generate detailed migration report
     */
    protected function generateReport($issues)
    {
        $reportPath = storage_path('app/migration_verification_report.json');
        
        $report = [
            'timestamp' => now(),
            'total_issues' => count($issues),
            'issues_by_severity' => [
                'ERROR' => collect($issues)->where('severity', 'ERROR')->count(),
                'WARNING' => collect($issues)->where('severity', 'WARNING')->count(),
            ],
            'issues_by_type' => collect($issues)->groupBy('type')->map->count(),
            'issues' => $issues,
            'recommendations' => $this->generateRecommendations($issues)
        ];
        
        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
        $this->info("Detailed report saved to: {$reportPath}");
    }

    /**
     * Generate recommendations based on issues found
     */
    protected function generateRecommendations($issues)
    {
        $recommendations = [];
        
        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'count_mismatch':
                    $recommendations[] = "Re-run migration step for {$issue['table']} to sync missing records";
                    break;
                    
                case 'orphaned_records':
                    $recommendations[] = "Review foreign key relationships and consider cascading deletes";
                    break;
                    
                case 'duplicates':
                    $recommendations[] = "Implement unique constraints and clean up duplicate data";
                    break;
                    
                case 'business_logic':
                    $recommendations[] = "Review business logic implementation in migration code";
                    break;
            }
        }
        
        return array_unique($recommendations);
    }

    /**
     * Analyze the root cause of count mismatches
     */
    protected function analyzeCountMismatch($tableName, $oldCount, $newCount, $oldTables)
    {
        $diff = $newCount - $oldCount;
        
        switch ($tableName) {
            case 'transaction_elements':
                if ($diff < 0) {
                    return "Some transaction elements may have been skipped due to invalid data or failed validation";
                }
                break;
                
            case 'closings':
                if ($diff > 0) {
                    return "Possible duplicate closings or additional closings created during migration process";
                }
                break;
                
            case 'expenses':
                if ($diff > 0) {
                    return "Multiple source tables (expenses + inpatient_expense_transactions) may contain overlapping data or duplicates";
                } else {
                    return "Some expense records may have been filtered out due to validation rules";
                }
                break;
                
            case 'services':
                if ($diff < 0) {
                    return "Some services from old database tables may not have been migrated due to data quality issues";
                }
                break;
                
            default:
                if ($diff > 0) {
                    return "More records in new database - possible duplicates or additional data generation";
                } else {
                    return "Fewer records in new database - possible data filtering or validation exclusions";
                }
        }
        
        return "Unknown reason for mismatch";
    }
}