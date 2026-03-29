<?php

namespace App\Models;

use App\Enum\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_number',
        'name',
        'category_id',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'vendor_name',
        'warranty_expiry',
        'assigned_to_department_id',
        'assigned_to_user_id',
        'location',
        'status',
        'disposed_at',
        'disposal_reason',
        'disposal_value',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
            'disposed_at' => 'date',
            'purchase_cost' => 'decimal:2',
            'disposal_value' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class, 'assigned_to_department_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignmentLogs(): HasMany
    {
        return $this->hasMany(AssetAssignmentLog::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(AssetMaintenanceLog::class);
    }

    public function depreciationEntries(): HasMany
    {
        return $this->hasMany(AssetDepreciationEntry::class);
    }

    public static function generateAssetNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->format('Y');

            $count = self::where('asset_number', 'like', "AST/{$year}/%")
                ->lockForUpdate()
                ->count();

            return sprintf('AST/%s/%04d', $year, $count + 1);
        });
    }
}
