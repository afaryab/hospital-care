<?php

namespace App\Enum;

enum TransactionElementType
{
    case OPD;
    case IND;
    case EMG;
    case LAB;
    case RAD;
    case DNT;
    case ULT;
    case PETTY_CASH;
    case VOUCHER_PAY;
    case IND_EXP;
}
