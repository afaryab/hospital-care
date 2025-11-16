<?php

namespace App\Enum;

enum TransactionElementType
{
    case OPD;
    case IND;
    case ENG;
    case LAB;
    case RAD;
    case DNT;
    case ULT;
    case EXP;
    case VOUCHER_PAY;
}
