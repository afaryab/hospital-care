<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Patient extends Model
{
    protected $fillable = [
        'id',
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
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');

            // Count how many patients have been created this month with PS numbers
            // Use FOR UPDATE to lock the table and prevent race conditions
            $count = self::where('ps_number', 'like', "PS/{$year}/{$month}/%")
                        ->lockForUpdate()
                        ->count();
            $count += 1; // Increment for the new patient

            // Pad the count to be 4 digits
            $count = str_pad($count, 4, '0', STR_PAD_LEFT);

            return "PS/{$year}/{$month}/{$count}";
        });
    }

    public function getAgeAttribute()
    {
        // User age days and created at to calculate age in years with respect to today
        if ($this->age_days !== null) {
            $createdAt = $this->created_at ?? Carbon::now();
            $birthDate = $createdAt->copy()->subDays($this->age_days);

            $formToday = Carbon::now();
            $ageInYears = $birthDate->diffInYears($formToday);
            return $ageInYears;
        }
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'patient_id', 'id');
    }

    public function treatments()
    {
        return $this->hasMany(ServiceOrder::class, 'patient_id', 'id');
    }
}
