<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseVoucher extends Model
{
    protected $fillable = [
        'old_id',
        'exp_category_id',
        'service_order_id',
        'payed_to',
        'amount',
        'created_at',
        'updated_at'
    ];
}
