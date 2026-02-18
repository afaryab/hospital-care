<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $fillable = [
        'id',
        'tr_number',
        'old_id',
        'closing_id',
        'created_by',
        'patient_id',
        'panel_id',
        'receaveable_id',
        'type',
        'income_or_expense',
        'amount',
        'amount_alphabetical',
        'orignal_amount',
        'customer_payed',
        'change',
        'edited_amount',

        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'year',
        'month',
        'day',
        'number',
    ];

    public function getYearAttribute()
    {
        return $this->tr_number_parts['year'] ?? null;
    }
    public function getMonthAttribute()
    {
        return $this->tr_number_parts['month'] ?? null;
    }
    public function getDayAttribute()
    {
        return $this->tr_number_parts['day'] ?? null;
    }

    public function getNumberAttribute()
    {
        return $this->tr_number_parts['number'] ?? null;
    }
    public function getTrNumberPartsAttribute()
    {
        if (empty($this->tr_number)) {
            return null;
        }

        $parts = explode('/', $this->tr_number);

        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'day' => $parts[3] ?? null,
            'number' => $parts[4] ?? null
        ];
    }

    public function updateCounter(): void
    {
        $closing = $this->closing;
        if ($closing) {
            $totalIncAmount = $closing->transactions()->where('income_or_expense','INCOME')->sum('amount');
            $totalExpAmount = $closing->transactions()->where('income_or_expense','EXPENSE')->sum('amount');
            $closing->closing_amount = $totalIncAmount - $totalExpAmount;
            $closing->expense_payed = $totalExpAmount;
            $closing->saveQuietly();
        }
    }

    public function elements()
    {
        return $this->hasMany(TransactionElement::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function receaveable()
    {
        return $this->belongsTo(Receaveable::class, 'receaveable_id');
    }

    public function closing()
    {
        return $this->belongsTo(Closing::class, 'closing_id');
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id');
    }




    public static function generateTransactionNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');

            // Count how many patients have been created this month with PS numbers
            // Use FOR UPDATE to lock the table and prevent race conditions
            $count = self::where('tr_number', 'like', "TR/{$year}/{$month}/{$day}%")
                        ->lockForUpdate()
                        ->count();
            $count += 1; // Increment for the new patient

            // Pad the count to be 4 digits
            $count = str_pad($count, 4, '0', STR_PAD_LEFT);

            return "TR/{$year}/{$month}/{$day}/{$count}";
        });
    }
}
