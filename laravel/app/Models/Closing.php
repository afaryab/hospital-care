<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Closing extends Model
{
    protected $fillable = [
        'old_id',
        'reception_id',
        'receptionist_id',
        'ct_number',
        'status',
        'opening_amount',
        'closing_amount',
        'closing_amount_cash',
        'closing_amount_cheque',
        'closing_amount_card',
        'expense_payed',
        'cash_recieving_time',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'year',
        'month',
        'number',
    ];

    protected $casts = [
    ];

    public function getYearAttribute()
    {
        return $this->ct_number_parts['year'] ?? null;
    }
    public function getMonthAttribute()
    {
        return $this->ct_number_parts['month'] ?? null;
    }
    public function getNumberAttribute()
    {
        return $this->ct_number_parts['number'] ?? null;
    }

    public function getCtNumberPartsAttribute()
    {
        if (empty($this->ct_number)) {
            return null;
        }
        
        $parts = explode('/', $this->ct_number);
        
        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'number' => $parts[3] ?? null
        ];
    }


    public static function generateCounterNumber(): string
    {
        $now = Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('m');

        // Count how many counters have been created this month
        $count = self::where('ct_number', 'like', "CT/{$year}/{$month}/%")->count();
        $count += 1; // Increment for the new counter

        // STRPAD the count to be 4 digits
        $count = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "CT/{$year}/{$month}/{$count}";
    }


    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(User::class, 'receptionist_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'closing_id');
    }

}
