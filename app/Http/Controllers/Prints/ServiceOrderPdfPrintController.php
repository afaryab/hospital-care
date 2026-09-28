<?php

namespace App\Http\Controllers\Prints;

use App\Enum\ServiceOrderTemplate;
use App\Helpers\PdfPageNumbers;
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
            'service.department:id,name,slug,service_order_template',
            'treatmentRecord.triage',
            'treatmentRecord.icd10Code:id,code,description',
            'treatmentRecord.attachments',
            'treatmentRecord.treatingDoctor',
            'treatmentRecord.vitalSigns',
            'deathCertificate',
            'referralCertificate',
            'birthCertificate.attendingDoctor',
        ])->findOrFail($id);

        $this->authorize('view', $serviceOrder);

        activity()
            ->causedBy($request->user())
            ->performedOn($serviceOrder)
            ->event('downloaded')
            ->log('Service order PDF printed');

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

        $html = view(self::resolveView($serviceOrder), [
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
            ->setCallbacks(PdfPageNumbers::callbacks())
            ->stream($fileName);
    }

    /**
     * The print view for a service order, most specific first:
     * pdfs/service/{service}, then pdfs/service_department/{department},
     * then the template chosen for the department, then the default.
     * Keys are the service/department slug, lower-cased with any
     * non-alphanumeric run turned into "_" (e.g. EMG → emg, M.O_MOR → m_o_mor).
     */
    public static function resolveView(ServiceOrder $serviceOrder): string
    {
        $service = $serviceOrder->service;
        $department = $service?->department;

        $candidates = array_filter([
            $service?->slug ? 'pdfs.service.'.self::viewKey($service->slug) : null,
            $department?->slug ? 'pdfs.service_department.'.self::viewKey($department->slug) : null,
        ]);

        foreach ($candidates as $candidate) {
            if (view()->exists($candidate)) {
                return $candidate;
            }
        }

        return ($department?->service_order_template ?? ServiceOrderTemplate::default())->view();
    }

    private static function viewKey(string $slug): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($slug)), '_');
    }
}
