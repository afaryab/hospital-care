<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receaveable extends Model
{
    protected $fillable = [
        'patient_id',
        'panel_id',
        'transaction_id',
        'amount',
        'orignal_amount',
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

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id');
    }

    // Get Service Order attached to transaction (if any)
    public function serviceOrder()
    {
        return $this->hasOneThrough(
            ServiceOrder::class,
            Transaction::class,
            'id', // Foreign key on transactions table...
            'transaction_id', // Foreign key on service_orders table...
            'transaction_id', // Local key on receaveables table...
            'id'  // Local key on transactions table...
        );
    }
}
