<?php

namespace App\Enum;

enum ServiceOrderStatus
{
    case OPEN;
    case CLOSED;
    case IN_PROGRESS;
    case TREATED;
    case REVIEWED;
    case REFERRED;
    case CANCELLED;
    case REFUNDED;
}
