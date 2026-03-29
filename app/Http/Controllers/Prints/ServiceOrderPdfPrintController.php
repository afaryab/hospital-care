<?php

namespace App\Http\Controllers\Prints;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceOrderPdfPrintController extends Controller
{
    /**
     * Stream the Service Order PDF.
     */
    public function stream(string $id, Request $request)
    {
        $serviceOrder = ServiceOrder::with(['patient', 'doctor'])
            ->findOrFail($id);

        $patient = $serviceOrder->patient;

        // return view('pdfs.serviceorder', [
        //     'serviceOrder' => $serviceOrder,
        //     'patient' => $patient,
        // ]);
        // dd($serviceOrder->doctor);

        $html = view('pdfs.serviceorder', [
            'serviceOrder' => $serviceOrder,
            'patient' => $patient,
        ])->render();

        $fileName = 'ED-Clinical-Performa-'.($serviceOrder->id ?? Str::uuid()).'.pdf';

        return Pdf::loadHTML($html)
            ->setPaper('A4')
            ->stream($fileName);
    }
}
