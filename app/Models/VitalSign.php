<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_record_id',
        'temperature',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse_rate',
        'respiratory_rate',
        'oxygen_saturation',
        'gcs',
        'blood_glucose',
        'bp_systolic',
        'bp_diastolic',
        'weight',
        'height',
        'recorded_at',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'oxygen_saturation' => 'decimal:2',
            'gcs' => 'integer',
            'blood_glucose' => 'decimal:1',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * The clinical forms and department APIs send `bp_systolic` /
     * `bp_diastolic`; the columns are `blood_pressure_*`. These aliases map
     * both ways — without them Eloquent silently dropped every BP reading.
     *
     * @var list<string>
     */
    protected $appends = ['bp_systolic', 'bp_diastolic'];

    protected function bpSystolic(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => $this->blood_pressure_systolic,
            set: fn ($value): array => ['blood_pressure_systolic' => $value === '' ? null : $value],
        );
    }

    protected function bpDiastolic(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => $this->blood_pressure_diastolic,
            set: fn ($value): array => ['blood_pressure_diastolic' => $value === '' ? null : $value],
        );
    }

    public function treatmentRecord(): BelongsTo
    {
        return $this->belongsTo(TreatmentRecord::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
