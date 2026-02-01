<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'old_id',
        'voucher_id',
        'exp_category_id',
        'type',
        'notes',
        'notes_json',
        'service_order_id',
        'payed_to',
        'payed_to_name',
        'amount',
        'amount_alphabetical',

        'patient_id',
        'doctor_id',
        
        'transaction_id',
        'transaction_element_id',

        'created_at',
        'updated_at',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'exp_category_id');
    }

    public function voucher()
    {
        return $this->belongsTo(ExpenseVoucher::class, 'voucher_id');
    }

    public function transactionElement()
    {
        return $this->belongsTo(TransactionElement::class, 'transaction_element_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }


}
