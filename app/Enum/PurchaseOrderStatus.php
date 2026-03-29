<?php

namespace App\Enum;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Received = 'received';
    case Cancelled = 'cancelled';
}
