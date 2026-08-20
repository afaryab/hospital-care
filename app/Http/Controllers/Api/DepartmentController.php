<?php

namespace App\Http\Controllers\Api;

use App\Enum\TreatmentOutcome;
use App\Http\Controllers\Controller;
use App\Models\Icd10Code;
use App\Models\ReferralCertificate;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\TreatmentAttachment;
use App\Models\TreatmentRecord;
use App\Models\TriageHistory;
use App\Models\VitalSign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Shared treatment-record handler for EMG, DNT, LAB (PTH), ULT, and XRAY
 * departments. All departments use the same save/finalize/status logic —
 * the department type is derived from the service order itself.
 */
class DepartmentController extends Controller
{
    public function saveTreatmentRecord(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $this->authorize('update', $serviceOrder);

        $isEmergency = $serviceOrder->type === 'EMG';
        $finalize = $request->boolean('finalize');

        // Discharging an EMG patient (finalize) is a doctor-only action —
        // nursing staff can chart (vitals, notes, treatment, medications)
        // but cannot close out the encounter. Enforced here, not just hidden
        // in the UI, since this endpoint has no route-level role middleware.
        if ($isEmergency && $finalize && ! auth()->user()->emergencyDoctorProfiles()->exists()) {
            abort(403, 'Only emergency doctors can discharge a patient.');
        }

        $data = $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'history_of_present_illness' => ['nullable', 'string', 'max:3000'],
            'examination_findings' => ['nullable', 'array'],
            'diagnosis_code' => ['nullable', 'string', 'max:50'],
            'icd10_code_id' => ['nullable', 'integer', Rule::exists('icd10_codes', 'id')->where('is_active', true)],
            'diagnosis_text' => ['nullable', 'string', 'max:500'],
            'treatment_plan' => ['nullable', 'string', 'max:3000'],
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.drug_name' => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.dose' => ['nullable', 'string', 'max:100'],
            'prescriptions.*.frequency' => ['nullable', 'string', 'max:100'],
            'prescriptions.*.duration' => ['nullable', 'string', 'max:100'],
            'prescriptions.*.route' => ['nullable', 'string', 'max:100'],
            'prescriptions.*.instructions' => ['nullable', 'string', 'max:500'],
            'prescriptions.*.given_at' => ['nullable', 'date'],
            'follow_up_date' => ['nullable', 'date'],
            'outcome' => [Rule::requiredIf($isEmergency && $finalize), 'nullable', new Enum(TreatmentOutcome::class)],
            'outcome_at' => [Rule::requiredIf($isEmergency && $finalize), 'nullable', 'date'],
            'outcome_notes' => ['nullable', 'string', 'max:2000'],
            'referral_to' => [
                Rule::requiredIf(fn () => $isEmergency && $finalize && $request->input('outcome') === TreatmentOutcome::Referred->value),
                'nullable', 'string', 'max:255',
            ],
            'referral_notes' => ['nullable', 'string', 'max:10000'],
            'department_specific_data' => ['nullable', 'array'],
            'dental_chart' => ['nullable', 'array'],
            'triage_id' => [Rule::requiredIf($isEmergency), 'nullable', 'integer', 'exists:triages,id'],
            'treated_at' => [Rule::requiredIf($isEmergency), 'nullable', 'date'],
            'finalize' => ['nullable', 'boolean'],
            'vitals' => ['nullable', 'array'],
            'vitals.temperature' => ['nullable', 'numeric'],
            'vitals.bp_systolic' => ['nullable', 'integer'],
            'vitals.bp_diastolic' => ['nullable', 'integer'],
            'vitals.pulse_rate' => ['nullable', 'integer'],
            'vitals.respiratory_rate' => ['nullable', 'integer'],
            'vitals.oxygen_saturation' => ['nullable', 'numeric'],
            'vitals.weight' => ['nullable', 'numeric'],
            'vitals.height' => ['nullable', 'numeric'],
        ]);

        unset($data['finalize']);

        $vitals = $data['vitals'] ?? null;
        unset($data['vitals']);

        // referral_notes (CKEditor-authored referral letter body) lives on
        // ReferralCertificate, not TreatmentRecord — pulled out of $data so
        // it doesn't hit an unknown-column error on create()/update().
        $referralNotes = $data['referral_notes'] ?? null;
        unset($data['referral_notes']);

        if (! empty($data['icd10_code_id'])) {
            $icd = Icd10Code::find($data['icd10_code_id']);
            if ($icd) {
                $data['diagnosis_code'] = $icd->code;
            }
        }

        $treatmentRecord = $serviceOrder->treatmentRecord;
        $previousTriageId = $treatmentRecord?->triage_id;

        if ($treatmentRecord) {
            $updateData = array_filter($data, fn ($v) => $v !== null);
            if ($finalize) {
                $updateData['is_finalized'] = true;
                $updateData['finalized_at'] = Carbon::now();
                $updateData['treated_at'] ??= Carbon::now();
            }
            $treatmentRecord->update($updateData);
        } else {
            $treatmentRecord = TreatmentRecord::create([
                'service_order_id' => $serviceOrder->id,
                'department_id' => $this->resolveDepartmentId($serviceOrder),
                'treating_doctor_id' => auth()->id(),
                'recorded_by' => auth()->id(),
                'treated_at' => $data['treated_at'] ?? Carbon::now(),
                'is_finalized' => $finalize,
                'finalized_at' => $finalize ? Carbon::now() : null,
                ...$data,
            ]);
        }

        if ($treatmentRecord->outcome === TreatmentOutcome::Referred && $referralNotes !== null) {
            // The observer already ensured a ReferralCertificate exists for
            // this outcome transition (fired synchronously during the save
            // above) — fill in the doctor-authored letter body.
            $serviceOrder->referralCertificate?->update([
                'notes' => ReferralCertificate::sanitizeNotes($referralNotes),
            ]);
        }

        if (array_key_exists('triage_id', $data) && $data['triage_id'] !== $previousTriageId) {
            TriageHistory::create([
                'treatment_record_id' => $treatmentRecord->id,
                'service_order_id' => $serviceOrder->id,
                'old_triage_id' => $previousTriageId,
                'new_triage_id' => $data['triage_id'],
                'changed_by' => auth()->id(),
                'changed_at' => Carbon::now(),
            ]);
        }

        if (! empty($vitals)) {
            VitalSign::create([
                'treatment_record_id' => $treatmentRecord->id,
                'temperature' => $vitals['temperature'] ?? null,
                'bp_systolic' => $vitals['bp_systolic'] ?? null,
                'bp_diastolic' => $vitals['bp_diastolic'] ?? null,
                'pulse_rate' => $vitals['pulse_rate'] ?? null,
                'respiratory_rate' => $vitals['respiratory_rate'] ?? null,
                'oxygen_saturation' => $vitals['oxygen_saturation'] ?? null,
                'weight' => $vitals['weight'] ?? null,
                'height' => $vitals['height'] ?? null,
                'recorded_at' => Carbon::now(),
                'recorded_by' => auth()->id(),
            ]);
        }

        if ($finalize) {
            $serviceOrder->update(['status' => 'treated']);
        } elseif (strtolower($serviceOrder->status) === 'open') {
            $serviceOrder->update(['status' => 'in-progress']);
        }

        return response()->json([
            'message' => $finalize ? 'Treatment record finalized.' : 'Treatment record saved.',
            'data' => $treatmentRecord->load('vitalSigns'),
        ]);
    }

    public function updateStatus(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:open,in-progress,treated,closed,cancelled'],
        ]);

        $serviceOrder->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Status updated.',
            'data' => $serviceOrder->only(['id', 'status']),
        ]);
    }

    /**
     * Return the latest queue for the given department types, most recent
     * first. Not scoped to "today" — a date-based cutoff caused the queue to
     * intermittently show empty despite orders existing (timezone bucketing
     * mismatch, and legitimately spans past midnight for departments like EMG).
     * Route: GET /api/{dept}/my-queue?types[]=EMG
     */
    public function myQueue(Request $request): JsonResponse
    {
        $user = $request->user();
        $types = $request->query('types', []);

        if (empty($types) || ! is_array($types)) {
            return response()->json(['data' => [], 'stats' => ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0]]);
        }

        $query = ServiceOrder::query()
            ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text,triage_id', 'treatmentRecord.triage:id,name,color'])
            ->whereIn('type', $types)
            ->orderByRaw("CASE WHEN LOWER(status) = 'in-progress' THEN 0 WHEN LOWER(status) = 'open' THEN 1 WHEN LOWER(status) = 'treated' THEN 2 ELSE 3 END ASC")
            ->orderBy('created_at', 'DESC');

        // For departments with a doctor assignment, scope to the logged-in user
        if ($request->boolean('doctor_scoped', true)) {
            $query->where('doctor_id', $user->id);
        }

        $orders = $query->limit(50)->get();

        return response()->json([
            'data' => $orders->values(),
            'stats' => [
                'open' => $orders->filter(fn ($o) => strtolower($o->status) === 'open')->count(),
                'in_progress' => $orders->filter(fn ($o) => strtolower($o->status) === 'in-progress')->count(),
                'treated' => $orders->filter(fn ($o) => in_array(strtolower($o->status), ['treated', 'closed']))->count(),
                'total' => $orders->count(),
            ],
        ]);
    }

    /**
     * Live search across SO number and patient name for a department.
     *
     * Rather than OR-ing leading-wildcard LIKEs across every column (a full
     * scan regardless of what was typed), the search term's shape tells us
     * exactly which single column to query:
     *   - "PS/{y}/{m}/{n}/{DEPT}/{seq}" (extra segments beyond the patient
     *     number) → so_number
     *   - "PS/{y}/{m}/{n}" (plain patient number) → patient.ps_number
     *   - starts with "SO" → so_number
     *   - starts with "TR" → transaction number, resolved to its service order(s)
     *   - "{DEPT}/{digits}" (e.g. "EMG/0000133" — the dashboard's search-box
     *     prefill, the department's current so_short minus its last digit) → so_short
     *   - all-digits → so_short
     *   - anything else → patient name
     *
     * Route: POST /api/{dept}/search
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:255'],
            'types' => ['required', 'array'],
            'types.*' => ['string'],
        ]);

        $q = trim($data['q']);
        $types = $data['types'];

        $query = ServiceOrder::query()
            ->with(['patient:id,name,ps_number', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text,triage_id', 'treatmentRecord.triage:id,name,color'])
            ->whereIn('type', $types);

        $upper = strtoupper($q);

        if (str_starts_with($upper, 'PS') && substr_count($q, '/') > 3) {
            // e.g. PS/2026/07/2620/EMG/00001334 — a full so_number
            $query->where('so_number', 'like', "{$q}%");
        } elseif (str_starts_with($upper, 'PS')) {
            // e.g. PS/2026/07/2620 — a plain patient ps_number
            $query->whereHas('patient', fn ($pq) => $pq->where('ps_number', 'like', "{$q}%"));
        } elseif (str_starts_with($upper, 'SO')) {
            $query->where('so_number', 'like', "{$q}%");
        } elseif (str_starts_with($upper, 'TR')) {
            $transactionIds = Transaction::where('tr_number', 'like', "{$q}%")->limit(50)->pluck('id');
            $serviceOrderIds = TransactionElement::whereIn('transaction_id', $transactionIds)
                ->whereNotNull('service_order_id')
                ->pluck('service_order_id');
            $query->whereIn('id', $serviceOrderIds);
        } elseif (preg_match('/^[A-Za-z]+\/\d+$/', $q)) {
            $query->where('so_short', 'like', "{$q}%");
        } elseif (ctype_digit($q)) {
            $query->where('so_short', 'like', "{$q}%");
        } else {
            $query->whereHas('patient', fn ($pq) => $pq->where('name', 'like', "%{$q}%"));
        }

        $results = $query->latest('id')->limit(20)->get();

        return response()->json(['data' => $results->values()]);
    }

    /**
     * Upload an image/PDF attachment for a treatment record (X-ray, Ultrasound, etc).
     * Route: POST /api/{dept}/service-orders/{serviceOrder}/attachments
     */
    public function uploadAttachment(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $this->authorize('update', $serviceOrder);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $treatmentRecord = $serviceOrder->treatmentRecord ?? TreatmentRecord::create([
            'service_order_id' => $serviceOrder->id,
            'department_id' => $this->resolveDepartmentId($serviceOrder),
            'treating_doctor_id' => auth()->id(),
            'recorded_by' => auth()->id(),
            'treated_at' => Carbon::now(),
        ]);

        $file = $data['file'];
        // Stored on the private disk — attachments are clinical images/PDFs
        // (PHI) and must never be reachable via a guessable public URL.
        // Served back out through showAttachment(), which is auth-checked.
        $path = $file->store("treatment-attachments/{$treatmentRecord->id}", 'local');

        $attachment = TreatmentAttachment::create([
            'treatment_record_id' => $treatmentRecord->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'label' => $data['label'] ?? null,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Attachment uploaded.',
            'data' => $attachment,
        ], 201);
    }

    /**
     * Stream a treatment attachment's bytes to an authorized viewer.
     * Route: GET /api/attachments/{attachment}
     */
    public function showAttachment(TreatmentAttachment $attachment): BinaryFileResponse
    {
        $this->authorize('view', $attachment->treatmentRecord->serviceOrder);

        if (! Storage::disk('local')->exists($attachment->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($attachment->file_path), [
            'Content-Type' => $attachment->file_type,
        ]);
    }

    /**
     * Delete a treatment attachment.
     * Route: DELETE /api/{dept}/attachments/{attachment}
     */
    public function deleteAttachment(TreatmentAttachment $attachment): JsonResponse
    {
        $this->authorize('update', $attachment->treatmentRecord->serviceOrder);

        Storage::disk('local')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted.']);
    }

    private function resolveDepartmentId(ServiceOrder $serviceOrder): int
    {
        return ServiceDepartment::where('slug', $serviceOrder->type)->value('id')
            ?? $serviceOrder->service?->service_department_id;
    }
}
