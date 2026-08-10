<?php

namespace App\Enum;

enum ServiceOrderTemplate: string
{
    case EmergencyTriageDetailed = 'emergency_triage_detailed';
    case EmergencyTriageCompact = 'emergency_triage_compact';

    public function label(): string
    {
        return match ($this) {
            self::EmergencyTriageDetailed => 'Emergency Triage - Detailed (2 Page)',
            self::EmergencyTriageCompact => 'Emergency Triage - Compact (1 Page)',
        };
    }

    public function view(): string
    {
        return match ($this) {
            self::EmergencyTriageDetailed => 'pdfs.serviceorder',
            self::EmergencyTriageCompact => 'pdfs.serviceorder-triage-compact',
        };
    }

    public static function default(): self
    {
        return self::EmergencyTriageDetailed;
    }
}
