<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Ward;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndDoctorController extends Controller
{
    /**
     * IND Doctor Dashboard — ward/bed map on left, unassigned queue on right.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isIndDoctor = $user->indDoctorProfiles()->exists();

        $wards = collect();
        $unassignedQueue = collect();

        if ($isIndDoctor) {
            // Load all wards with rooms → beds → active assignment → patient
            $wards = Ward::query()
                ->with([
                    'rooms.beds.activeAssignment.patient:id,name,ps_number,gender,age_days,age_dob',
                    'rooms.beds.activeAssignment.serviceOrder:id,so_number,status,doctor_id,created_at',
                ])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // IND service orders with no active bed assignment — these are in queue
            $assignedServiceOrderIds = BedAssignment::query()
                ->where('status', 'active')
                ->pluck('service_order_id');

            $unassignedQueue = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name'])
                ->where('type', 'IND')
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->whereNotIn('id', $assignedServiceOrderIds)
                ->latest('created_at')
                ->limit(50)
                ->get();
        }

        return Inertia::render('ind/index', [
            'isIndDoctor' => $isIndDoctor,
            'wards' => $wards,
            'unassignedQueue' => $unassignedQueue->values(),
            'searchPrefill' => ServiceOrder::latestSoShortPrefix(['IND'], 'IND'),
        ]);
    }

    /**
     * Show a specific service order's indoor patient file.
     */
    public function show(Request $request, int $id): Response
    {
        $serviceOrder = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number,gender,age_days,age_dob,contact',
                'service:id,name',
                'doctor:id,name',
                'treatmentRecord.vitalSigns',
            ])
            ->findOrFail($id);

        // Active bed assignment for this SO
        $bedAssignment = BedAssignment::query()
            ->with(['bed:id,bed_number,room_id', 'bed.room:id,name,ward_id', 'bed.room.ward:id,name', 'ward:id,name', 'room:id,name'])
            ->where('service_order_id', $id)
            ->where('status', 'active')
            ->first();

        // Available beds for assignment dropdown
        $availableBeds = Bed::query()
            ->with(['room:id,name,ward_id', 'room.ward:id,name'])
            ->where('status', 'available')
            ->where('is_active', true)
            ->get(['id', 'bed_number', 'room_id', 'ward_id']);

        // Previous IND visits
        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', 'IND')
            ->where('id', '!=', $id)
            ->latest('created_at')
            ->limit(10)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('ind/patient', [
            'serviceOrder' => $serviceOrder,
            'bedAssignment' => $bedAssignment,
            'availableBeds' => $availableBeds->values(),
            'previousVisits' => $previousVisits,
        ]);
    }
}
