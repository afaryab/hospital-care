<?php

namespace Processton\Abacus\Models;

use Illuminate\Database\Eloquent\Model;

class AbacusTransaction extends Model
{
    protected $fillable = [
        'abacus_incoming_id',
        'abacus_chart_of_account_id',
        'abacus_year_id',
        'amount',
        'date',
        'entry_type',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function incoming()
    {
        return $this->belongsTo(AbacusIncoming::class, 'abacus_incoming_id');
    }

    public function account()
    {
        return $this->belongsTo(AbacusChartOfAccount::class, 'abacus_chart_of_account_id');
    }

    public function year()
    {
        return $this->belongsTo(AbacusYear::class, 'abacus_year_id');
    }
}
