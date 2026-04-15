<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\VitalSign;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IndController extends Controller
{
    /**
     * Search for IND service orders by SO number or patient PS number.
     */
    public function search(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $query = trim($filters['q']);
        $limit = $filters['limit'] ?? 10;

        $exact = collect();
        $possible = collect();

        $exactSo = ServiceOrder::query()
            ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
            ->where('type', 'IND')
            ->where(function ($q) use ($query) {
                $q->where('so_number', $query)->orWhere('so_short', $query);
            })
            ->first();

        if ($exactSo) {
            $exact->push($exactSo);
        }

        $patient = Patient::query()->where('ps_number', $query)->first();

        if ($patient) {
            $patientOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
                ->where('type', 'IND')
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

        if ($exact->isEmpty() && $possible->isEmpty()) {
            $possible = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
                ->where('type', 'IND')
                ->where('so_number', 'like', "%{$query}%")
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
     * Assign a bed to an IND service order.
     */
    public function assignBed(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $data = $request->validate([
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Release any previous active assignment for this SO
        BedAssignment::query()
            ->where('service_order_id', $serviceOrder->id)
            ->where('status', 'active')
            ->update(['status' => 'transferred', 'discharged_at' => Carbon::now()]);

        $bed = Bed::with('room.ward')->findOrFail($data['bed_id']);

        $assignment = BedAssignment::create([
            'bed_id' => $bed->id,
            'ward_id' => $bed->ward_id,
            'room_id' => $bed->room_id,
            'patient_id' => $serviceOrder->patient_id,
            'service_order_id' => $serviceOrder->id,
            'assigned_by' => auth()->id(),
            'admitted_at' => Carbon::now(),
            'status' => 'active',
            'notes' => $data['notes'] ?? null,
        ]);

        // Update SO status to in-progress if still open
        if (strtolower($serviceOrder->status) === 'open') {
            $serviceOrder->update(['status' => 'in-progress']);
        }

        return response()->json([
            'message' => "Bed {$bed->bed_number} assigned successfully.",
            'data' => $assignment->load(['bed.room.ward', 'ward', 'room']),
        ]);
    }

    /**
     * Discharge a patient from their bed.
     */
    public function discharge(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $assignment = BedAssignment::query()
            ->where('service_order_id', $serviceOrder->id)
            ->where('status', 'active')
            ->firstOrFail();

        $assignment->update([
            'status' => 'discharged',
            'discharged_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Patient discharged from bed.',
        ]);
    }

    /**
     * Get live ward snapshot — used for the left panel.
     */
    public function wardSnapshot(): JsonResponse
    {
        $wards = Ward::query()
            ->with([
                'rooms.beds.activeAssignment.patient:id,name,ps_number,gender,age_days,age_dob',
                'rooms.beds.activeAssignment.serviceOrder:id,so_number,status,created_at',
            ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $wards]);
    }

    /**
     * Save or update treatment record for an IND service order.
     */
    public function saveTreatmentRecord(Request $request, ServiceOrder $serviceOrder): JsonResponse
    {
        $data = $request->validate([
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'history_of_present_illness' => ['nullable', 'string', 'max:5000'],
            'examination_findings' => ['nullable', 'array'],
            'diagnosis_code' => ['nullable', 'string', 'max:50'],
            'diagnosis_text' => ['nullable', 'string', 'max:500'],
            'treatment_plan' => ['nullable', 'string', 'max:5000'],
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
            'message' => $finalize ? 'Record finalized.' : 'Draft saved.',
            'data' => $treatmentRecord->load('vitalSigns'),
        ]);
    }

    /**
     * Update service order status.
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
}
