<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanelCheque extends Model
{
    protected $fillable = [
        'panel_id',
        'bank_account_id',
        'cheque_number',
        'amount',
        'due_date',
        'status',
        'received_at',
        'notes',
        'linked_receaveable_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'received_at' => 'datetime',
        ];
    }

    public function panel(): BelongsTo
    {
        return $this->belongsTo(Panel::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function linkedReceaveable(): BelongsTo
    {
        return $this->belongsTo(Receaveable::class, 'linked_receaveable_id');
    }
}
