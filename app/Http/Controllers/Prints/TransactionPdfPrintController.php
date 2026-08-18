<?php

namespace App\Http\Controllers\Prints;

use App\Http\Controllers\Controller;
use App\Models\HospitalSetting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionPdfPrintController extends Controller
{
    /**
     * Generate and stream transaction PDF
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @param  int  $number
     * @return Response
     */
    public function stream(Request $request, $year, $month, $day, $number)
    {

        $trNumber = 'TR/'.$year.'/'.$month.'/'.$day.'/'.$number;

        // Find the transaction by date components and number
        $transaction = Transaction::where('tr_number', $trNumber)
            ->with(['patient', 'receaveable', 'elements.doctor', 'elements.expenseCategory', 'elements.service', 'elements.serviceRecestation', 'elements.serviceOrder', 'elements.doctor', 'closing', 'closing.receptionist', 'closing.reception'])
            ->first();

        if (! $transaction) {
            abort(404, 'Transaction not found');
        }

        $this->authorize('view', $transaction);

        // Get variant from query parameter (full, dot-printer, or thermal)
        $variant = $request->get('variant', 'full');
        $variant = in_array($variant, ['full', 'dot-printer', 'thermal']) ? $variant : 'full';

        // Prepare data for PDF
        $data = [
            'transaction' => $transaction,
            'patient' => $transaction->patient,
            'receaveable' => $transaction->receaveable,
            'items' => $transaction->elements,
            'counter' => $transaction->closing,
            'variant' => $variant,
            'generated_at' => Carbon::now(),
            'hospital_info' => [
                'name' => HospitalSetting::get('hospital_name', config('app.name', 'Hospital Management System')),
                'address' => HospitalSetting::get('hospital_address', config('hospital.address', 'Hospital Address')),
                'phone' => HospitalSetting::get('hospital_phone', config('hospital.phone', '+1-234-567-8900')),
                'email' => HospitalSetting::get('hospital_email', config('hospital.email', 'info@hospital.com')),
                'ntn' => HospitalSetting::get('hospital_ntn'),
                'strn' => HospitalSetting::get('hospital_strn'),
            ],
        ];
        // Select the appropriate view based on variant
        $view = match ($variant) {
            'dot-printer' => 'pdfs.transaction.transaction-dot-printer',
            'thermal' => 'pdfs.transaction.transaction-thermal',
            default => 'pdfs.transaction.transaction-full',
        };

        // Set paper size and orientation based on variant
        $paperConfig = match ($variant) {
            'thermal' => ['size' => [0, 0, 226.77, 441.89], 'orientation' => 'portrait'], // 80mm width, long height
            'dot-printer' => ['size' => 'a4', 'orientation' => 'portrait'], // Standard A4 for dot matrix
            default => ['size' => 'a4', 'orientation' => 'portrait'], // Full A4 report
        };

        // Set margins based on variant
        $margins = match ($variant) {
            'thermal' => ['top' => 0, 'bottom' => 0, 'left' => 0, 'right' => 0],
            'dot-printer' => ['top' => 5, 'bottom' => 5, 'left' => 8, 'right' => 8],
            default => ['top' => 15, 'bottom' => 15, 'left' => 20, 'right' => 20],
        };

        // Generate PDF
        $pdf = Pdf::loadView($view, $data);

        // Set paper configuration based on variant
        if ($variant === 'thermal') {
            // For thermal printer: 80mm width, minimal height
            $pdf->setPaper([0, 0, 226.77, 441.89], 'portrait'); // 80mm x 297mm in points
        } else {
            // Standard A4 for other variants
            $pdf->setPaper($paperConfig['size'], $paperConfig['orientation']);
        }

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => $variant === 'thermal' ? 'helvetica' : 'sans-serif',
            'margin-top' => $margins['top'],
            'margin-bottom' => $margins['bottom'],
            'margin-left' => $margins['left'],
            'margin-right' => $margins['right'],
            'dpi' => $variant === 'thermal' ? 96 : 96, // Higher DPI for thermal printers
        ]);

        // Generate filename
        $filename = sprintf(
            'transaction_%s_%02d_%02d_%03d_%s.pdf',
            $year,
            $month,
            $day,
            $number,
            $variant
        );

        // return view($view, $data);

        // Stream the PDF
        return $pdf->stream($filename);
    }

    /**
     * Download transaction PDF
     *
     * @param  int  $year
     * @param  int  $month
     * @param  int  $day
     * @param  int  $number
     * @return Response
     */
    public function download(Request $request, $year, $month, $day, $number)
    {
        $trNumber = 'TR/'.$year.'/'.$month.'/'.$day.'/'.$number;

        // Find the transaction by date components and number
        $transaction = Transaction::where('tr_number', $trNumber)
            ->with(['patient', 'receaveable', 'elements.doctor', 'elements.expense', 'elements.expense.category', 'elements.service', 'elements.serviceRecestation', 'elements.serviceOrder', 'elements.doctor', 'closing', 'closing.receptionist', 'closing.reception'])
            ->first();

        if (! $transaction) {
            abort(404, 'Transaction not found');
        }

        $this->authorize('view', $transaction);

        $variant = $request->get('variant', 'full');
        $variant = in_array($variant, ['full', 'dot-printer', 'thermal']) ? $variant : 'full';

        $data = [
            'transaction' => $transaction,
            'patient' => $transaction->patient,
            'receaveable' => $transaction->receaveable,
            'items' => $transaction->elements,
            'counter' => $transaction->closing,
            'variant' => $variant,
            'generated_at' => Carbon::now(),
            'hospital_info' => [
                'name' => HospitalSetting::get('hospital_name', config('app.name', 'Hospital Management System')),
                'address' => HospitalSetting::get('hospital_address', config('hospital.address', 'Hospital Address')),
                'phone' => HospitalSetting::get('hospital_phone', config('hospital.phone', '+1-234-567-8900')),
                'email' => HospitalSetting::get('hospital_email', config('hospital.email', 'info@hospital.com')),
                'ntn' => HospitalSetting::get('hospital_ntn'),
                'strn' => HospitalSetting::get('hospital_strn'),
            ],
        ];

        $view = match ($variant) {
            'dot-printer' => 'pdf.transaction.transaction-dot-printer',
            'thermal' => 'pdf.transaction.transaction-thermal',
            default => 'pdf.transaction.transaction-full',
        };

        // Set paper size and orientation based on variant
        $paperConfig = match ($variant) {
            'thermal' => ['size' => [0, 0, 226.77, 441.89], 'orientation' => 'portrait'], // 80mm x 200mm in points
            'dot-printer' => ['size' => 'a4', 'orientation' => 'portrait'], // Standard A4 for dot matrix
            default => ['size' => 'a4', 'orientation' => 'portrait'], // Full A4 report
        };

        // Set margins based on variant
        $margins = match ($variant) {
            'thermal' => ['top' => 2, 'bottom' => 2, 'left' => 2, 'right' => 2],
            'dot-printer' => ['top' => 5, 'bottom' => 5, 'left' => 8, 'right' => 8],
            default => ['top' => 15, 'bottom' => 15, 'left' => 20, 'right' => 20],
        };

        $pdf = Pdf::loadView($view, $data);

        // Set paper configuration based on variant
        if ($variant === 'thermal') {
            // For thermal printer: 80mm width, auto height
            $pdf->setPaper([0, 0, 226.77, 441.89], 'portrait'); // 80mm x 200mm in points
        } else {
            // Standard A4 for other variants
            $pdf->setPaper($paperConfig['size'], $paperConfig['orientation']);
        }

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => $variant === 'thermal' ? 'courier' : 'sans-serif',
            'margin-top' => $margins['top'],
            'margin-bottom' => $margins['bottom'],
            'margin-left' => $margins['left'],
            'margin-right' => $margins['right'],
            'dpi' => $variant === 'thermal' ? 96 : 96,
        ]);

        $filename = sprintf(
            'transaction_%s_%02d_%02d_%03d_%s.pdf',
            $year,
            $month,
            $day,
            $number,
            $variant
        );

        return $pdf->download($filename);
    }
}
