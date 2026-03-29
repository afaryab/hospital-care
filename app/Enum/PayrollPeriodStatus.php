<?php

namespace App\Enum;

enum PayrollPeriodStatus: string
{
    case Draft = 'draft';
    case Calculated = 'calculated';
    case Approved = 'approved';
    case Paid = 'paid';
    case Closed = 'closed';
}
