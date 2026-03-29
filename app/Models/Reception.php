<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Reception extends Model
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
        'id',
        'name',
        'allowed_departments',
        'is_allowed_to_pay_voucher',
        'is_allowed_to_pay_from_petty_cash',
        'is_cash_allowed',
        'is_cheques_allowed',
        'is_card_allowed',
    ];

    protected $casts = [
        'allowed_departments' => 'json',
        'is_allowed_to_pay_voucher' => 'boolean',
        'is_allowed_to_pay_from_petty_cash' => 'boolean',
        'is_cash_allowed' => 'boolean',
        'is_cheques_allowed' => 'boolean',
        'is_card_allowed' => 'boolean',
    ];

    public function closings()
    {
        return $this->hasMany(Closing::class);
    }
}
