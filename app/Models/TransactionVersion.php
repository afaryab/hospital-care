<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
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

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
