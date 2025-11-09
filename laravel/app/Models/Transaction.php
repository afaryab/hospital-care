<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'old_id',
        'closing_id',
        'created_by',
        'patient_id',
        'type',
        'income_or_expense',
        'amount',
        'amount_alphabetical',
        'orignal_amount',
        'customer_payed',
        'change',
        'edited_amount',

        'created_at',
        'updated_at',
    ];

    public function elements()
    {
        return $this->hasMany(TransactionElement::class);
    }
}
