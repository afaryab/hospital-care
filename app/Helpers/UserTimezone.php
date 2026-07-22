<?php

namespace App\Helpers;

use DateTimeZone;

class UserTimezone
{
    public static function current(): string
    {
        $user = auth()->user();

        if (! empty($user?->timezone)) {
            return $user->timezone;
        }

        $browserTz = request()->cookie('browser_timezone');

        if ($browserTz && in_array($browserTz, DateTimeZone::listIdentifiers())) {
            return $browserTz;
        }

        return 'Asia/Karachi';
    }
}
