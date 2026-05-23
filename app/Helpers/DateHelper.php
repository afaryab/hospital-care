<?php

namespace App\Helpers;

use App\Models\HospitalSetting;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateHelper
{
    private static ?string $cachedTimezone = null;

    public static function timezone(): string
    {
        if (self::$cachedTimezone === null) {
            $value = HospitalSetting::get('hospital_timezone', 'Asia/Karachi');
            self::$cachedTimezone = is_string($value) && $value !== '' ? $value : 'Asia/Karachi';
        }

        return self::$cachedTimezone;
    }

    public static function flushTimezoneCache(): void
    {
        self::$cachedTimezone = null;
    }

    public static function tz(mixed $date): ?CarbonInterface
    {
        if ($date === null || $date === '') {
            return null;
        }

        $carbon = $date instanceof CarbonInterface ? $date->copy() : Carbon::parse($date);

        return $carbon->setTimezone(self::timezone());
    }

    public static function pdfFormat(mixed $date, string $format): string
    {
        $converted = self::tz($date);

        return $converted ? $converted->format($format) : '';
    }
}
