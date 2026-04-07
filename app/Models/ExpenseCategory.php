<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_id',
        'name',
        'type',
        'pay_doc',
        'pay_others',
        'pay_users',
        'pay_patient',
        'allow_petty_cash',
        'allow_voucher',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pay_doc' => 'boolean',
        'pay_others' => 'boolean',
        'pay_users' => 'boolean',
        'pay_patient' => 'boolean',
        'allow_petty_cash' => 'boolean',
        'allow_voucher' => 'boolean',
    ];
}
