<?php

namespace App\Models;

use App\Casts\SafeEncryptedJson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id',
        'snapshot',
        'change_reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            // See PatientVersion — getOriginal() decrypts the parent's
            // encrypted notes_json before it reaches this snapshot, so
            // it's encrypted again going in.
            'snapshot' => SafeEncryptedJson::class,
            'changed_at' => 'datetime',
        ];
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
