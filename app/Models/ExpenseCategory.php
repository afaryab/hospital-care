<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ExpenseCategory extends Model
{
    use Cacheable, HasFactory;

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

    /**
     * The full expense category list, used by expense voucher and transaction
     * forms. Small and rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }
}
