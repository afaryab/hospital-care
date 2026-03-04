<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionElement extends Model
{
    protected $fillable = [
        'id',
        'old_id',
        'closing_id',
        'transaction_id',
        'created_by',
        'patient_id',
        'service_id',
        'service_recestation_id',
        'doctor_id',
        'expense_id',
        'exp_voucher_id',
        'type',
        'income_or_expense',
        'amount',
        'orignal_amount',
        'customer_payed',
        'change',
        'edited_amount',
        'refunded_transaction_id',
        
        'service_order_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the service order associated with this transaction element.
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * Get the transaction that owns the transaction element.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user associated with the transaction element.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the patient associated with the transaction element.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the service associated with the transaction element.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the service recestation associated with the transaction element.
     */
    public function serviceRecestation(): BelongsTo
    {
        return $this->belongsTo(ServiceRecestation::class, 'service_recestation_id');
    }

    /**
     * Get the doctor assigned to the transaction element.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function expVoucher(): BelongsTo
    {
        return $this->belongsTo(ExpenseVoucher::class, 'exp_voucher_id');
    }
    
}
