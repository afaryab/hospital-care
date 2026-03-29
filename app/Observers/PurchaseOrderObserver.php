<?php

namespace App\Observers;

use App\Models\PurchaseOrder;

class PurchaseOrderObserver
{
    public function creating(PurchaseOrder $purchaseOrder): void
    {
        if (empty($purchaseOrder->po_number)) {
            $purchaseOrder->po_number = PurchaseOrder::generatePoNumber();
        }
    }
}
