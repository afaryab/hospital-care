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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'pay_doc' => 'boolean',
        'pay_others' => 'boolean',
        'pay_users' => 'boolean',
    ];
}
