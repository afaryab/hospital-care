<?php

namespace App\Http\Controllers;

use App\Helpers\TreatmentFormConfig;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Triage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmergencyDoctorController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isEmgDoctor = $user->emergencyDoctorProfiles()->exists();
        $isEmgNurse = $user->nursingStaffProfiles()->exists();
        $hasAccess = $isEmgDoctor || $isEmgNurse;

        $recentOrders = collect();
        $todayStats = ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0];

        if ($hasAccess) {
            $query = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text,triage_id', 'treatmentRecord.triage:id,name,color'])
                ->where('type', 'EMG')
                ->orderByRaw("CASE WHEN LOWER(status) = 'in-progress' THEN 0 WHEN LOWER(status) = 'open' THEN 1 WHEN LOWER(status) = 'treated' THEN 2 ELSE 3 END ASC")
                ->orderBy('created_at', 'DESC')
                ->limit(30);

            // Nurses aren't assigned specific patients by doctor_id — they
            // work the whole EMG queue, not a per-doctor slice of it.
            if ($isEmgDoctor) {
                $query->where('doctor_id', $user->id);
            }

            $recentOrders = $query->get();

            $todayStats = [
                'open' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'open')->count(),
                'in_progress' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'in-progress')->count(),
                'treated' => $recentOrders->filter(fn ($o) => in_array(strtolower($o->status), ['treated', 'closed']))->count(),
                'total' => $recentOrders->count(),
            ];
        }

        return Inertia::render('emg/index', [
            'isEmgDoctor' => $hasAccess,
            'isDoctorScoped' => $isEmgDoctor,
            'recentOrders' => $recentOrders->values(),
            'todayStats' => $todayStats,
            'searchPrefill' => ServiceOrder::latestSoShortPrefix(['EMG'], 'EMG'),
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $serviceOrder = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number,gender,age_days,age_dob,contact',
                'service:id,name,treatment_form_config',
                'doctor:id,name',
                'treatmentRecord.vitalSigns',
                'treatmentRecord.triage',
                'treatmentRecord.attachments',
                'treatmentRecord.triageHistories.oldTriage:id,name,color',
                'treatmentRecord.triageHistories.newTriage:id,name,color',
                'treatmentRecord.triageHistories.changedBy:id,name',
            ])
            ->findOrFail($id);

        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized,triage_id', 'treatmentRecord.triage:id,name,color'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', 'EMG')
            ->where('id', '!=', $serviceOrder->id)
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('emg/patient', [
            'serviceOrder' => $serviceOrder,
            'previousVisits' => $previousVisits,
            'formConfig' => TreatmentFormConfig::resolve('EMG', $serviceOrder->service?->treatment_form_config),
            'triages' => Triage::cachedActive(),
            'canDischarge' => $request->user()->emergencyDoctorProfiles()->exists(),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (empty($query)) {
            return redirect()->route('emg-dashboard');
        }

        $so = ServiceOrder::query()->where('type', 'EMG')
            ->where(fn ($q) => $q->where('so_number', $query)->orWhere('so_short', $query))
            ->first();

        if ($so) {
            return redirect()->route('emg-patient', ['id' => $so->id]);
        }

        $patient = Patient::query()->where('ps_number', $query)->first();
        if ($patient) {
            $so = ServiceOrder::query()
                ->where('patient_id', $patient->id)->where('type', 'EMG')
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest()->first();
            if ($so) {
                return redirect()->route('emg-patient', ['id' => $so->id]);
            }
        }

        return redirect()->route('emg-dashboard')->with('searchError', "No active Emergency order found for \"{$query}\".");
    }
}
