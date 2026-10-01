<?php

namespace App\Enum;

enum SlipPhotoSubject: string
{
    case Patient = 'patient';
    case Guardian = 'guardian';

    public function label(): string
    {
        return match ($this) {
            self::Patient => 'Patient',
            self::Guardian => 'Guardian',
        };
    }
}
