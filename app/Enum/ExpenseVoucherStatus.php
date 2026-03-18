<?php

namespace App\Enum;

enum ExpenseVoucherStatus: string
{
    case PENDING = 'pending';
    case PAYED = 'payed';
}
