<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use App\Casts\SafeEncryptedJson;
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
        'triage_id',
        'chief_complaint',
        'history_of_present_illness',
        'examination_findings',
        'diagnosis_code',
        'icd10_code_id',
        'diagnosis_text',
        'treatment_plan',
        'prescriptions',
        'follow_up_date',
        'outcome',
        'outcome_at',
        'outcome_notes',
        'referral_to',
        'department_specific_data',
        'dental_chart',
        'treated_at',
        'recorded_by',
        'is_finalized',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'chief_complaint' => SafeEncrypted::class,
            'history_of_present_illness' => SafeEncrypted::class,
            'diagnosis_text' => SafeEncrypted::class,
            'treatment_plan' => SafeEncrypted::class,
            'outcome_notes' => SafeEncrypted::class,
            'referral_to' => SafeEncrypted::class,
            'examination_findings' => SafeEncryptedJson::class,
            'prescriptions' => SafeEncryptedJson::class,
            'department_specific_data' => SafeEncryptedJson::class,
            'dental_chart' => SafeEncryptedJson::class,
            'is_finalized' => 'boolean',
            'treated_at' => 'datetime',
            'finalized_at' => 'datetime',
            'follow_up_date' => 'date',
            'outcome' => TreatmentOutcome::class,
            'outcome_at' => 'datetime',
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

    public function icd10Code(): BelongsTo
    {
        return $this->belongsTo(Icd10Code::class, 'icd10_code_id');
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }

    public function triage(): BelongsTo
    {
        return $this->belongsTo(Triage::class);
    }

    public function triageHistories(): HasMany
    {
        // Tie-break on id: sequential changes within the same second would
        // otherwise sort ambiguously on changed_at alone.
        return $this->hasMany(TriageHistory::class)->latest('changed_at')->latest('id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TreatmentAttachment::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TreatmentRecordVersion::class)->latest('changed_at');
    }
}
