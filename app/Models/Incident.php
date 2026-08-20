<?php

namespace App\Models;

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Enum\IncidentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Incident extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'type',
        'severity',
        'status',
        'user_id',
        'patient_id',
        'department_id',
        'reported_by',
        'assigned_to',
        'ip_address',
        'device_signature',
        'context',
        'occurred_at',
        'classified_at',
        'assigned_at',
        'investigated_at',
        'investigation_notes',
        'resolved_at',
        'resolution_notes',
        'closed_at',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => IncidentType::class,
            'severity' => IncidentSeverity::class,
            'status' => IncidentStatus::class,
            'context' => 'array',
            'occurred_at' => 'datetime',
            'classified_at' => 'datetime',
            'assigned_at' => 'datetime',
            'investigated_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Incident $incident): void {
            if (! $incident->isDirty('status')) {
                return;
            }

            $original = $incident->getOriginal('status');
            $expectedNext = $original->next();

            if ($expectedNext === null || $incident->status !== $expectedNext) {
                throw ValidationException::withMessages([
                    'status' => "An incident can only move from \"{$original->label()}\" to \"".($expectedNext?->label() ?? 'nothing — it is already closed').'".',
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
