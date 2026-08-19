<?php

namespace App\Models;

use App\Enum\ConsentMethod;
use App\Enum\ConsentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Consent extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'patient_id',
        'service_order_id',
        'consent_type',
        'consent_method',
        'consented_at',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'consent_type' => ConsentType::class,
            'consent_method' => ConsentMethod::class,
            'consented_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Consent $consent): void {
            if ($consent->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for consent records.');
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
