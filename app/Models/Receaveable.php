<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Receaveable extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

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

    /**
     * The service order this receivable is attached to, via its transaction's
     * income line item. ServiceOrder has no transaction_id column, so this
     * goes through TransactionElement (the real link) rather than Transaction.
     */
    public function serviceOrder(): HasOneThrough
    {
        return $this->hasOneThrough(
            ServiceOrder::class,
            TransactionElement::class,
            'transaction_id', // Foreign key on transaction_elements -> transactions.id
            'id', // Foreign key on service_orders -> id
            'transaction_id', // Local key on receaveables table
            'service_order_id' // Local key on transaction_elements table
        );
    }

    /**
     * Payment transactions recorded against this receivable (partial/full
     * settlements), distinct from the original transaction that created it.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receaveable_id');
    }
}
