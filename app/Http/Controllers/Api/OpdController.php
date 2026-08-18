<?php

namespace App\Http\Controllers\Api;

use App\Enum\TreatmentOutcome;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Icd10Code;
use App\Models\Patient;
use App\Models\ReferralCertificate;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\VitalSign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class OpdController extends Controller
{
    /**
     * Search for service orders by SO number or patient PS number.
     * Used by the dashboard search box via fetch.
     */
    public function search(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $query = trim($filters['q']);
        $limit = $filters['limit'] ?? 20;

        $exact = collect();
        $possible = collect();

        // Exact SO number match
        $exactSo = ServiceOrder::query()
            ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
            ->where('type', 'OPD')
            ->where(function ($q) use ($query) {
                $q->where('so_number', $query)
                    ->orWhere('so_short', $query);
            })
            ->first();

        if ($exactSo) {
            $exact->push($exactSo);
        }

        // Patient PS number match → their open OPD orders
        $patient = Patient::query()->where('ps_number', $query)->first();

        if ($patient) {
            $patientOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
                ->where('type', 'OPD')
                ->where('patient_id', $patient->id)
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest('created_at')
                ->limit($limit)
                ->get();

            foreach ($patientOrders as $order) {
                if (! $exact->contains('id', $order->id)) {
                    $possible->push($order);
                }
            }
        }

        // Partial SO number / so_short match — so_short is what the dashboard
        // pre-fills the search box with (department prefix + running
        // sequence minus the last digit), so staff only need to type the end.
        if ($exact->isEmpty() && $possible->isEmpty()) {
            $possible = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
                ->where('type', 'OPD')
                ->where(fn ($q) => $q->where('so_number', 'like', "%{$query}%")
                    ->orWhere('so_short', 'like', "{$query}%"))
                ->latest('created_at')
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'data' => [
                'exact' => $exact->values(),
                'possible' => $possible->values(),
            ],
        ]);
    }

    /**
     * Save or update the treatment record for a service order.
     */
    public function saveTreatmentRecord(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
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
            'follow_up_date' => ['nullable', 'date'],
            'outcome' => ['nullable', 'string', 'max:50'],
            'referral_to' => ['nullable', 'string', 'max:255'],
            'referral_notes' => ['nullable', 'string', 'max:10000'],
            'department_specific_data' => ['nullable', 'array'],
            'finalize' => ['nullable', 'boolean'],
            // Vital signs
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

        $finalize = (bool) ($data['finalize'] ?? false);
        unset($data['finalize']);

        $vitals = $data['vitals'] ?? null;
        unset($data['vitals']);

        $referralNotes = $data['referral_notes'] ?? null;
        unset($data['referral_notes']);

        // Auto-sync diagnosis_code from the ICD-10 FK when provided.
        if (! empty($data['icd10_code_id'])) {
            $icd = Icd10Code::find($data['icd10_code_id']);
            if ($icd) {
                $data['diagnosis_code'] = $icd->code;
            }
        }

        $treatmentRecord = $serviceOrder->treatmentRecord;

        if ($treatmentRecord) {
            $updateData = array_filter($data, fn ($v) => $v !== null);

            if ($finalize) {
                $updateData['is_finalized'] = true;
                $updateData['finalized_at'] = Carbon::now();
                $updateData['treated_at'] = $updateData['treated_at'] ?? Carbon::now();
            }

            $treatmentRecord->update($updateData);
        } else {
            $treatmentRecord = TreatmentRecord::create([
                'service_order_id' => $serviceOrder->id,
                'treating_doctor_id' => auth()->id(),
                'recorded_by' => auth()->id(),
                'treated_at' => Carbon::now(),
                'is_finalized' => $finalize,
                'finalized_at' => $finalize ? Carbon::now() : null,
                ...$data,
            ]);
        }

        if ($treatmentRecord->outcome === TreatmentOutcome::Referred && $referralNotes !== null) {
            $serviceOrder->referralCertificate?->update([
                'notes' => ReferralCertificate::sanitizeNotes($referralNotes),
            ]);
        }

        // Save vitals if provided
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

        // Auto update SO status
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

    /**
     * Update the status of a service order (e.g. call patient, mark treated).
     */
    public function updateStatus(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:open,in-progress,treated,closed,cancelled'],
        ]);

        $serviceOrder->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Status updated.',
            'data' => ['status' => $serviceOrder->fresh()->status],
        ]);
    }

    /**
     * Get the doctor's queue for today with live counts.
     */
    public function myQueue(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = ServiceOrder::query()
            ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
            ->where('type', 'OPD')
            ->where('doctor_id', $user->id)
            ->whereBetween('created_at', DateHelper::todayRangeUtc())
            ->orderByRaw("CASE WHEN LOWER(status) = 'in-progress' THEN 0 WHEN LOWER(status) = 'open' THEN 1 WHEN LOWER(status) = 'treated' THEN 2 ELSE 3 END ASC")
            ->orderBy('created_at', 'ASC')
            ->get();

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
}
