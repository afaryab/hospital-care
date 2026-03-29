<?php

namespace App\Models;

use App\Enum\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'type',
        'description',
        'cost',
        'performed_by',
        'scheduled_date',
        'completed_date',
        'next_maintenance_date',
    ];

    protected function casts(): array
    {
        return [
            'type' => MaintenanceType::class,
            'cost' => 'decimal:2',
            'scheduled_date' => 'date',
            'completed_date' => 'date',
            'next_maintenance_date' => 'date',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
