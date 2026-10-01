<?php

namespace App\Helpers;

use App\Models\Patient;
use Illuminate\Support\Carbon;

class PatientDisplay
{
    public const GENDERS = ['m' => 'Male', 'f' => 'Female', 't' => 'Transgender', 'o' => 'Other'];

    /**
     * Age with the most useful unit: years from 1 year up, months below a
     * year, days below a month — e.g. "18 Y", "7 M", "12 D".
     */
    public static function age(?Patient $patient): ?string
    {
        if ($patient === null) {
            return null;
        }

        $birth = match (true) {
            $patient->age_dob !== null => Carbon::parse($patient->age_dob),
            $patient->age_days !== null => ($patient->created_at ?? now())->copy()->subDays((int) $patient->age_days),
            default => null,
        };

        if ($birth === null) {
            return null;
        }

        $now = now();

        return match (true) {
            ($years = (int) $birth->diffInYears($now)) >= 1 => "{$years} Y",
            ($months = (int) $birth->diffInMonths($now)) >= 1 => "{$months} M",
            default => ((int) $birth->diffInDays($now)).' D',
        };
    }

    public static function sex(?Patient $patient): ?string
    {
        return self::GENDERS[$patient?->gender] ?? null;
    }

    /**
     * "18 Y · Male"; missing parts are left out.
     */
    public static function ageSex(?Patient $patient): string
    {
        return implode(' · ', array_filter([self::age($patient), self::sex($patient)]));
    }
}
