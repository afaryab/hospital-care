<?php

namespace App\Jobs;

use App\Models\ExpenseVoucher;
use App\Models\HospitalSetting;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsProvisioningService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Renders the voucher PDF (reusing the same Dompdf pipeline the other
 * Prints\* controllers use) and files it into the paid-to doctor's DMS
 * folder, so "doctor folder contains expense-voucher PDFs" is a real,
 * persisted file rather than something regenerated on every view.
 */
class PersistExpenseVoucherPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $expenseVoucherId) {}

    public function handle(DmsProvisioningService $provisioning, DmsDocumentService $documents): void
    {
        $voucher = ExpenseVoucher::query()->with(['payedTo', 'expCategory'])->find($this->expenseVoucherId);

        if (! $voucher || ! $voucher->payedTo) {
            return;
        }

        $pdf = Pdf::loadView('pdfs.expense-voucher.expense-voucher', [
            'voucher' => $voucher,
            'hospital_info' => [
                'name' => HospitalSetting::get('hospital_name', config('app.name')),
            ],
        ]);

        $tmpPath = tempnam(sys_get_temp_dir(), 'voucher-pdf').'.pdf';
        file_put_contents($tmpPath, $pdf->output());

        $folder = $provisioning->doctorFolder($voucher->payedTo);
        $uploaded = new UploadedFile($tmpPath, str_replace('/', '-', $voucher->vc_number).'.pdf', 'application/pdf', null, true);

        $documents->upload($uploaded, $folder, $voucher->payedTo);

        @unlink($tmpPath);
    }
}
