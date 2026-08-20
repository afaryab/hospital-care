<?php

namespace App\Enum;

enum ConsentType: string
{
    case Treatment = 'treatment';
    case Procedure = 'procedure';
    case DataSharing = 'data_sharing';

    public function label(): string
    {
        return match ($this) {
            self::Treatment => 'Treatment',
            self::Procedure => 'Procedure',
            self::DataSharing => 'Data Sharing',
        };
    }
}
