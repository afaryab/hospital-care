<?php

namespace App\Enum;

enum SalaryAdvanceStatus: string
{
    case Active = 'active';
    case FullyRecovered = 'fully_recovered';
    case WrittenOff = 'written_off';
}
