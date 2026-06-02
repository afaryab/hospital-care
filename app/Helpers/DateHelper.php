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

    /**
     * Resolve the UTC instant for the START of a day that the user picked in the
     * hospital timezone. Use for the lower bound of a datetime-column filter.
     *
     * A bare date string is interpreted in the hospital timezone (not UTC), so
     * "2026-05-23" becomes 2026-05-22 19:00:00 UTC for Asia/Karachi (+05:00).
     */
    public static function dayStartUtc(mixed $date): ?CarbonInterface
    {
        return self::inHospitalTimezone($date)?->startOfDay()->utc();
    }

    /**
     * Resolve the UTC instant for the END of a day that the user picked in the
     * hospital timezone. Use for the (inclusive) upper bound of a datetime-column
     * filter.
     */
    public static function dayEndUtc(mixed $date): ?CarbonInterface
    {
        return self::inHospitalTimezone($date)?->endOfDay()->utc();
    }

    /**
     * UTC range [start, end] covering "today" as seen in the hospital timezone.
     * Use with whereBetween on a datetime column to scope a query to today.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public static function todayRangeUtc(): array
    {
        $today = Carbon::now(self::timezone());

        return [self::dayStartUtc($today), self::dayEndUtc($today)];
    }

    /**
     * Interpret a filter value as a moment in the hospital timezone.
     *
     * Strings are parsed AS hospital-local time (the user picked them in their
     * own timezone); existing Carbon instances are converted into it.
     */
    private static function inHospitalTimezone(mixed $date): ?CarbonInterface
    {
        if ($date === null || $date === '') {
            return null;
        }

        return $date instanceof CarbonInterface
            ? $date->copy()->setTimezone(self::timezone())
            : Carbon::parse($date, self::timezone());
    }

    public static function pdfFormat(mixed $date, string $format): string
    {
        $converted = self::tz($date);

        return $converted ? $converted->format($format) : '';
    }
}
