<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MigrationLog extends Model
{
    protected $fillable = [
        'migration_step',
        'action_type',
        'old_table',
        'old_record_id',
        'new_table',
        'new_record_id',
        'reason',
        'old_data',
        'new_data',
        'error_details',
        'old_amount',
        'new_amount',
        'validation_errors',
        'migration_time',
        'batch_id',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'validation_errors' => 'array',
        'migration_time' => 'datetime',
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    // Action type constants
    const ACTION_SUCCESS = 'success';

    const ACTION_SKIPPED = 'skipped';

    const ACTION_DUPLICATED = 'duplicated';

    const ACTION_ERROR = 'error';

    const ACTION_WARNING = 'warning';

    const ACTION_VALIDATION_FAILED = 'validation_failed';

    const ACTION_SANITIZED = 'sanitized';

    /**
     * Log a migration action
     */
    public static function logAction($migrationStep, $actionType, $data = [])
    {
        return self::create(array_merge([
            'migration_step' => $migrationStep,
            'action_type' => $actionType,
            'migration_time' => now(),
            'batch_id' => session('migration_batch_id', uniqid('batch_')),
        ], $data));
    }

    /**
     * Log a successful migration
     */
    public static function logSuccess($migrationStep, $oldTable, $oldId, $newTable, $newId, $oldData = null, $newData = null)
    {
        return self::logAction($migrationStep, self::ACTION_SUCCESS, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'new_table' => $newTable,
            'new_record_id' => $newId,
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }

    /**
     * Log a skipped record
     */
    public static function logSkipped($migrationStep, $oldTable, $oldId, $reason, $oldData = null)
    {
        return self::logAction($migrationStep, self::ACTION_SKIPPED, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'reason' => $reason,
            'old_data' => $oldData,
        ]);
    }

    /**
     * Log an error
     */
    public static function logError($migrationStep, $oldTable, $oldId, $error, $oldData = null)
    {
        return self::logAction($migrationStep, self::ACTION_ERROR, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'reason' => 'Migration error occurred',
            'error_details' => $error,
            'old_data' => $oldData,
        ]);
    }

    /**
     * Log a validation failure
     */
    public static function logValidationFailure($migrationStep, $oldTable, $oldId, $validationErrors, $oldData = null)
    {
        return self::logAction($migrationStep, self::ACTION_VALIDATION_FAILED, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'reason' => 'Validation failed',
            'validation_errors' => $validationErrors,
            'old_data' => $oldData,
        ]);
    }

    /**
     * Log data sanitization
     */
    public static function logSanitized($migrationStep, $oldTable, $oldId, $newTable, $newId, $changes, $oldData = null, $newData = null)
    {
        return self::logAction($migrationStep, self::ACTION_SANITIZED, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'new_table' => $newTable,
            'new_record_id' => $newId,
            'reason' => 'Data sanitized: '.implode(', ', $changes),
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }

    /**
     * Log financial amount tracking
     */
    public static function logFinancial($migrationStep, $oldTable, $oldId, $newTable, $newId, $oldAmount, $newAmount, $actionType = self::ACTION_SUCCESS)
    {
        return self::logAction($migrationStep, $actionType, [
            'old_table' => $oldTable,
            'old_record_id' => $oldId,
            'new_table' => $newTable,
            'new_record_id' => $newId,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'reason' => $oldAmount != $newAmount ? 'Amount changed during migration' : 'Amount preserved',
        ]);
    }
}
