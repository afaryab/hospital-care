<?php

namespace App\Enum;

use App\Models\HospitalSetting;

enum AppointmentPriorityMode: string
{
    case Priority = 'priority';
    case Medium = 'medium';
    case Standard = 'standard';

    public static function current(): self
    {
        return self::tryFrom(HospitalSetting::get('appointment_priority_mode', self::Standard->value)) ?? self::Standard;
    }

    public function label(): string
    {
        return match ($this) {
            self::Priority => 'Priority (guaranteed slot, draft receivable held)',
            self::Medium => 'Medium (reserved token, identity confidential until check-in)',
            self::Standard => 'Standard (informational only, normal walk-in order)',
        };
    }
}
