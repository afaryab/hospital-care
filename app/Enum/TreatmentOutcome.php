<?php

namespace App\Enum;

enum TreatmentOutcome: string
{
    case Improved = 'improved';
    case Unchanged = 'unchanged';
    case Deteriorated = 'deteriorated';
    case Discharged = 'discharged';
    case Referred = 'referred';
    case Expired = 'expired';
}
