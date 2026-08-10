<?php

namespace App\Enum;

enum DeathCertificateManner: string
{
    case Natural = 'natural';
    case Accident = 'accident';
    case Suicide = 'suicide';
    case Homicide = 'homicide';
    case Undetermined = 'undetermined';

    public function label(): string
    {
        return match ($this) {
            self::Natural => 'Natural',
            self::Accident => 'Accident',
            self::Suicide => 'Suicide',
            self::Homicide => 'Homicide',
            self::Undetermined => 'Undetermined',
        };
    }
}
