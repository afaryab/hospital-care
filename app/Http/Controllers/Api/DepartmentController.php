<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Icd10Code;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\VitalSign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Shared treatment-record handler for EMG, DNT, LAB (PTH), ULT, and XRAY
 * departments. All departments use the same save/finalize/status logic —
 * the department type is derived from the service order itself.
 */
class DepartmentController extends Controller
{
    public function saveTreatmentRecord(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $data = $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'history_of_present_illness' => ['nullable', 'string', 'max:3000'],
            'examination_findings' => ['nullable', 'array'],
            'diagnosis_code' => ['nullable', 'string', 'max:50'],
            'icd10_code_id' => ['nullable', 'integer', 'exists:icd10_codes,id'],
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
            'department_specific_data' => ['nullable', 'array'],
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

        $finalize = (bool) ($data['finalize'] ?? false);
        unset($data['finalize']);

        $vitals = $data['vitals'] ?? null;
        unset($data['vitals']);

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
                $updateData['treated_at'] ??= Carbon::now();
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
     * Return today's queue for the given department types.
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
            ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
            ->whereIn('type', $types)
            ->whereDate('created_at', Carbon::today())
            ->orderByRaw("FIELD(status, 'in-progress', 'IN-PROGRESS', 'open', 'OPEN', 'treated', 'TREATED') ASC")
            ->orderBy('created_at', 'ASC');

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

        $results = ServiceOrder::query()
            ->with(['patient:id,name,ps_number', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
            ->whereIn('type', $types)
            ->where(function ($query) use ($q) {
                $query->where('so_number', 'like', "%{$q}%")
                    ->orWhere('so_short', 'like', "%{$q}%")
                    ->orWhereHas('patient', fn ($pq) => $pq->where('name', 'like', "%{$q}%")
                        ->orWhere('ps_number', 'like', "%{$q}%"));
            })
            ->latest('id')
            ->limit(15)
            ->get();

        return response()->json(['data' => $results->values()]);
    }
}
