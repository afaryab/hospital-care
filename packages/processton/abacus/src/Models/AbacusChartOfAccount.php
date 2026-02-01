<?php

namespace Processton\Abacus\Models;

use Illuminate\Database\Eloquent\Model;

class AbacusChartOfAccount extends Model
{
    protected $fillable = [
        'code', 'name', 'base_type', 'type', 'parent_id', 'is_group',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->hasMany(AbacusTransaction::class, 'abacus_chart_of_account_id');
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
