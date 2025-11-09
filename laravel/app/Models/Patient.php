<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'ps_number',
        'name',
        'gender',
        'age_group',
        'age_days',
        'age_dob',
        'address',
        'guardian',
        'relation',
        'contact',
        'cnic',
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'year',
        'month',
        'number',
    ];

    public function getYearAttribute()
    {
        return $this->ps_number_parts['year'] ?? null;
    }
    public function getMonthAttribute()
    {
        return $this->ps_number_parts['month'] ?? null;
    }
    public function getNumberAttribute()
    {
        return $this->ps_number_parts['number'] ?? null;
    }

    public function getPsNumberPartsAttribute()
    {
        if (empty($this->ps_number)) {
            return null;
        }

        $parts = explode('/', $this->ps_number);

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
        $count = self::where('ps_number', 'like', "{$year}-{$month}-%")->count();
        $count += 1; // Increment for the new counter

        // STRPAD the count to be 4 digits
        $count = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "PS/{$year}/{$month}/{$count}";
    }
}
