<?php

namespace App\Observers;

use App\Models\Service;
use App\Models\TransactionElement;
use App\Models\ServiceOrder;
use Carbon\Carbon;

class TransactionElementObserver
{
    /**
     * Handle the TransactionElement "created" event.
     */
    public function created(TransactionElement $transactionElement): void
    {
        if($transactionElement->service_id){
            // Generate unique SO number

            $service = Service::find($transactionElement->service_id);
            $patient = $transactionElement->patient;

            if($patient == null || $service == null){
                return;
            }
            $soNumber = $patient->ps_number . '/' . $service->department->slug . '/' . $this->generateServiceOrderNumber();

            // Create ServiceOrder when TransactionElement is created
            $order = ServiceOrder::create([
                'type' => $service->department->slug,
                'so_number' => $soNumber,
                'created_by' => $transactionElement->created_by,
                'patient_id' => $transactionElement->patient_id,
                'service_id' => $transactionElement->service_id,
                'service_recestation_id' => $transactionElement->service_recestation_id,
                'doctor_id' => $transactionElement->doctor_id,
                'is_composit' => $service->is_composit_service, // Default to false
                'notes' => "Auto-generated service order for transaction element #{$transactionElement->id}",
                'notes_json' => []
            ]);

            $transactionElement->service_order_id = $order->id;
            $transactionElement->save();
        }
    }

    /**
     * Handle the TransactionElement "updated" event.
     */
    public function updated(TransactionElement $transactionElement): void
    {
        // Optionally update the corresponding ServiceOrder
        $serviceOrder = ServiceOrder::where('id', $transactionElement->service_order_id)->first();
        
        if ($serviceOrder) {
            $serviceOrder->update([
                'patient_id' => $transactionElement->patient_id,
                'service_id' => $transactionElement->service_id,
                'service_recestation_id' => $transactionElement->service_recestation_id,
                'doctor_id' => $transactionElement->doctor_id,
                'notes_json' => []
            ]);
        }
    }

    /**
     * Handle the TransactionElement "deleted" event.
     */
    public function deleted(TransactionElement $transactionElement): void
    {
        // Optionally delete or mark the corresponding ServiceOrder as cancelled
        $serviceOrder = ServiceOrder::where('id', $transactionElement->service_order_id)->first();
        
        if ($serviceOrder) {
            // You can choose to either delete the service order or mark it as cancelled
            // Option 1: Delete the service order
            // $serviceOrder->delete();
            
            // Option 2: Mark as cancelled (requires adding a status field to service_orders table)
            $serviceOrder->update([
                'notes' => ($serviceOrder->notes ?? '') . " - CANCELLED: Transaction element deleted",
                'notes_json' => []
            ]);
        }
    }

    /**
     * Generate a unique service order number
     */
    private function generateServiceOrderNumber(): string
    {

        // Check how many service orders have been created this month where created_at is in the current month
        $count = ServiceOrder::where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('created_at', '<=', Carbon::now()->endOfMonth())
            ->count();

        $count += 1; // Increment for the new service order

        // STRPAD the count to be 8 digits
        $count = str_pad($count, 8, '0', STR_PAD_LEFT);
        return  $count;
    }
}