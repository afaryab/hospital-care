<?php

namespace App\Models;

use App\Casts\SafeEncryptedJson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentRecordVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_record_id',
        'snapshot',
        'change_reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            // See PatientVersion — getOriginal() decrypts the parent's
            // SafeEncrypted/SafeEncryptedJson clinical fields before it
            // reaches this snapshot, so it's encrypted again going in.
            'snapshot' => SafeEncryptedJson::class,
            'changed_at' => 'datetime',
        ];
    }

    public function treatmentRecord(): BelongsTo
    {
        return $this->belongsTo(TreatmentRecord::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
