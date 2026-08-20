<?php

namespace App\Models;

use App\Casts\SafeEncryptedJson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'snapshot',
        'change_reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            // getOriginal() on the parent Patient decrypts SafeEncrypted
            // fields (cnic/contact/address) before it reaches this
            // snapshot — encrypt it going in so the audit trail doesn't
            // itself become a plaintext copy of the PII it's meant to be
            // logging changes to.
            'snapshot' => SafeEncryptedJson::class,
            'changed_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
