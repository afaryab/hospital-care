<?php

namespace App\Http\Controllers\Prints;

use App\Enum\ServiceOrderTemplate;
use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
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
        $serviceOrder = ServiceOrder::with([
            'patient',
            'doctor',
            'service.department:id,name,service_order_template',
            'treatmentRecord.triage',
            'treatmentRecord.attachments',
            'treatmentRecord.treatingDoctor',
            'treatmentRecord.vitalSigns',
            'deathCertificate',
            'referralCertificate',
            'birthCertificate.attendingDoctor',
        ])->findOrFail($id);

        $this->authorize('view', $serviceOrder);

        $patient = $serviceOrder->patient;

        // Past History: the patient's last 6 diagnosed conditions from other
        // service orders, distinct from the doctor-entered History on this visit.
        $pastDiagnoses = TreatmentRecord::query()
            ->whereHas('serviceOrder', fn ($q) => $q->where('patient_id', $serviceOrder->patient_id)
                ->where('id', '!=', $serviceOrder->id))
            ->where(fn ($q) => $q->whereNotNull('diagnosis_code')->orWhereNotNull('icd10_code_id'))
            ->with('icd10Code:id,code,description')
            ->latest('treated_at')
            ->limit(6)
            ->get(['id', 'service_order_id', 'diagnosis_code', 'icd10_code_id', 'treated_at']);

        $template = $serviceOrder->service?->department?->service_order_template ?? ServiceOrderTemplate::default();

        $html = view($template->view(), [
            'serviceOrder' => $serviceOrder,
            'patient' => $patient,
            'pastDiagnoses' => $pastDiagnoses,
        ])->render();

        $extraPages = '';

        if ($certificate = $serviceOrder->deathCertificate) {
            $extraPages .= view('pdfs.death-certificate', [
                'serviceOrder' => $serviceOrder,
                'patient' => $patient,
                'certificate' => $certificate,
            ])->render();
        }

        if ($referral = $serviceOrder->referralCertificate) {
            $extraPages .= view('pdfs.referral-certificate', [
                'serviceOrder' => $serviceOrder,
                'patient' => $patient,
                'referral' => $referral,
            ])->render();
        }

        // Birth certificates are admin-authored and only print once reviewed
        // and locked — an unlocked draft never appears on the SO printout.
        if (($birth = $serviceOrder->birthCertificate) && $birth->is_locked) {
            $extraPages .= view('pdfs.birth-certificate', [
                'serviceOrder' => $serviceOrder,
                'patient' => $patient,
                'certificate' => $birth,
            ])->render();
        }

        if ($extraPages !== '') {
            // The base templates are full HTML documents; splice the extra
            // pages in before the closing </body> so dompdf parses one
            // document instead of concatenated, technically-invalid markup.
            $html = str_contains($html, '</body>')
                ? str_replace('</body>', $extraPages.'</body>', $html)
                : $html.$extraPages;
        }

        $fileName = 'ED-Clinical-Performa-'.($serviceOrder->id ?? Str::uuid()).'.pdf';

        return Pdf::loadHTML($html)
            ->setPaper('A4')
            ->stream($fileName);
    }
}
