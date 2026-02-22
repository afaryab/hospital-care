<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'old_id',
        'name',
        'type',
        'pay_doc',
        'pay_others',
        'pay_users',
        'created_at',
        'updated_at',
    ];
}
