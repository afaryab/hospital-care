<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_record_id',
        'service_order_id',
        'old_triage_id',
        'new_triage_id',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function treatmentRecord(): BelongsTo
    {
        return $this->belongsTo(TreatmentRecord::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function oldTriage(): BelongsTo
    {
        return $this->belongsTo(Triage::class, 'old_triage_id');
    }

    public function newTriage(): BelongsTo
    {
        return $this->belongsTo(Triage::class, 'new_triage_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
