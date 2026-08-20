<?php

namespace App\Enum;

enum IncidentSeverity: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Critical',
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => 'danger',
            self::High => 'warning',
            self::Medium => 'info',
            self::Low => 'gray',
        };
    }
}
