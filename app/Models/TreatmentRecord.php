<?php

namespace App\Models;

use App\Enum\TreatmentOutcome;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class TreatmentRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_order_id',
        'department_id',
        'treating_doctor_id',
        'chief_complaint',
        'history_of_present_illness',
        'examination_findings',
        'diagnosis_code',
        'diagnosis_text',
        'treatment_plan',
        'prescriptions',
        'follow_up_date',
        'outcome',
        'referral_to',
        'department_specific_data',
        'treated_at',
        'recorded_by',
        'is_finalized',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'examination_findings' => 'json',
            'prescriptions' => 'json',
            'department_specific_data' => 'json',
            'is_finalized' => 'boolean',
            'treated_at' => 'datetime',
            'finalized_at' => 'datetime',
            'follow_up_date' => 'date',
            'outcome' => TreatmentOutcome::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (TreatmentRecord $treatmentRecord): void {
            $dirtyAttributes = array_diff(array_keys($treatmentRecord->getDirty()), ['updated_at']);

            if ($treatmentRecord->getOriginal('is_finalized') && count($dirtyAttributes) > 0) {
                throw ValidationException::withMessages([
                    'treatment_record' => 'Finalized treatment records cannot be modified.',
                ]);
            }

            TreatmentRecordVersion::query()->create([
                'treatment_record_id' => $treatmentRecord->id,
                'snapshot' => $treatmentRecord->getOriginal(),
                'change_reason' => 'record_update',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        });

        static::deleting(function (TreatmentRecord $treatmentRecord): void {
            if ($treatmentRecord->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for treatment records.');
            }
        });
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class, 'department_id');
    }

    public function treatingDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'treating_doctor_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TreatmentRecordVersion::class)->latest('changed_at');
    }
}
