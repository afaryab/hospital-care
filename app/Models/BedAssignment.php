<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BedAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bed_id',
        'ward_id',
        'room_id',
        'patient_id',
        'service_order_id',
        'assigned_by',
        'admitted_at',
        'discharged_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'admitted_at' => 'datetime',
            'discharged_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // When a bed assignment becomes active, mark the bed as occupied
        static::created(function (BedAssignment $assignment): void {
            if ($assignment->status === 'active') {
                $assignment->bed->update(['status' => 'occupied']);
            }
        });

        // When a bed assignment is discharged/transferred, free the bed
        static::updated(function (BedAssignment $assignment): void {
            if ($assignment->isDirty('status') && in_array($assignment->status, ['discharged', 'transferred'])) {
                $assignment->bed->update(['status' => 'available']);
            }
        });
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
