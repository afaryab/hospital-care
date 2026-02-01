<?php

namespace Processton\Abacus\Models;

use Illuminate\Database\Eloquent\Model;

class AbacusIncoming extends Model
{
    protected $fillable = [
        'reference',
        'date',
        'description',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(AbacusTransaction::class);
    }

    public function debits()
    {
        return $this->transactions()->where('entry_type', 'debit');
    }

    public function credits()
    {
        return $this->transactions()->where('entry_type', 'credit');
    }

    public function isConverted(): bool
    {
        return $this->transactions()->count() > 0;
    }

    public function getTotalDebitsAttribute(): float
    {
        return (float) $this->debits()->sum('amount');
    }

    public function getTotalCreditsAttribute(): float
    {
        return (float) $this->credits()->sum('amount');
    }

    public function isBalanced(): bool
    {
        if (!$this->isConverted()) {
            return false;
        }
        
        return abs($this->getTotalDebitsAttribute() - $this->getTotalCreditsAttribute()) < 0.01;
    }
}
