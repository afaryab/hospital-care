<?php

namespace App\Enum;

enum ConsentMethod: string
{
    case DigitalCheckbox = 'digital_checkbox';
    case PaperSigned = 'paper_signed';
    case VerbalRecorded = 'verbal_recorded';

    public function label(): string
    {
        return match ($this) {
            self::DigitalCheckbox => 'Digital Checkbox',
            self::PaperSigned => 'Paper Signed',
            self::VerbalRecorded => 'Verbal Recorded',
        };
    }
}
