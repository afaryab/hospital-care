<?php

namespace App\Enum;

enum SlipPhotoSource: string
{
    case Manual = 'manual';
    case Auto = 'auto';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Snapped by receptionist',
            self::Auto => 'Captured automatically',
        };
    }
}
