<?php

namespace Processton\Abacus\Models;

use Illuminate\Database\Eloquent\Model;

class AbacusYear extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'integer',
    ];

    protected $appends = [
        'debit',
        'credit',
        'balance',
    ];

    public function isActive(): bool
    {
        return $this->status === 1;
    }

    public function isArchived(): bool
    {
        return $this->status === 2;
    }

    public function transactions()
    {
        return $this->hasMany(AbacusTransaction::class);
    }

    public function getDebitAttribute()
    {
        return $this->transactions()->where('entry_type', 'debit')->sum('amount');
    }

    public function getCreditAttribute()
    {
        return $this->transactions()->where('entry_type', 'credit')->sum('amount');
    }
    public function getBalanceAttribute()
    {
        return $this->credit - $this->debit;
    }
}
