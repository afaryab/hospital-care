<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receaveable extends Model
{
    protected $fillable = [
        'patient_id',
        'transaction_id',
        'amount',
        'due_date',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
