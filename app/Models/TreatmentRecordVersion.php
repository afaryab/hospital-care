<?php

namespace App\Models;

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
            'snapshot' => 'array',
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
