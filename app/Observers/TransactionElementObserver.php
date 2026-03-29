<?php

namespace App\Observers;

use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\TransactionElement;
use Carbon\Carbon;

class TransactionElementObserver
{
    /**
     * Handle the TransactionElement "created" event.
     */
    public function created(TransactionElement $transactionElement): void
    {
        if ($transactionElement->service_id) {
            // Generate unique SO number
            $service = Service::find($transactionElement->service_id);
            $patient = $transactionElement->patient;

            if ($patient == null || $service == null || $service->receaveable_id != null) {
                return;
            }

            $s = ServiceOrder::generateServiceOrderNumber($transactionElement->type);
            $ss = ServiceOrder::generateShortServiceOrderNumber($transactionElement->type);

            $soShort = $service->department->slug.'/'.str_pad($ss, 8, '0', STR_PAD_LEFT);
            $soNumber = $patient->ps_number.'/'.$service->department->slug.'/'.str_pad($s, 8, '0', STR_PAD_LEFT);

            $token = Carbon::now()->format('Ym').str_pad($s, 6, '0', STR_PAD_LEFT);
            $payee = null;
            // Order payee set to patient
            if ($transactionElement->transaction->type == 'PANEL') {
                $payee = $transactionElement->transaction->panel;
            } else {
                $payee = $transactionElement->transaction->patient;
            }

            // Create ServiceOrder when TransactionElement is created
            $order = ServiceOrder::create([
                'type' => $transactionElement->type,
                'token' => $token, // You can use the same as so_short or generate a different token if needed
                'so_number' => $soNumber,
                'so_short' => $soShort,
                'created_by' => $transactionElement->created_by,
                'patient_id' => $transactionElement->patient_id,
                'service_id' => $transactionElement->service_id,
                'service_recestation_id' => $transactionElement->service_recestation_id,
                'doctor_id' => $transactionElement->doctor_id,
                'is_composit' => $service->is_composit_service, // Default to false
                'notes' => "Auto-generated service order for transaction element #{$transactionElement->id}",
                'notes_json' => [],
                'payee_type' => get_class($payee),
                'payee_id' => $payee->id,
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
                'notes_json' => [],
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
                'notes' => ($serviceOrder->notes ?? '').' - CANCELLED: Transaction element deleted',
                'notes_json' => [],
            ]);
        }
    }
}
