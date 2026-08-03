<?php

namespace App\Enum;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case CheckedIn = 'checked_in';
    case NoShow = 'no_show';
    case Cancelled = 'cancelled';
}
