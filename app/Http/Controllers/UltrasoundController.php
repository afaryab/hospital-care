<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\TreatmentFormConfig;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UltrasoundController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isUltDoctor = $user->ultrasoundDoctorProfiles()->exists();

        $recentOrders = collect();
        $todayStats = ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0];

        if ($isUltDoctor) {
            $recentOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
                ->where('type', 'ULT')
                ->where('doctor_id', $user->id)
                ->whereBetween('created_at', DateHelper::todayRangeUtc())
                ->orderByRaw("CASE WHEN LOWER(status) = 'in-progress' THEN 0 WHEN LOWER(status) = 'open' THEN 1 WHEN LOWER(status) = 'treated' THEN 2 ELSE 3 END ASC")
                ->orderBy('created_at', 'DESC')
                ->limit(30)
                ->get();

            $todayStats = [
                'open' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'open')->count(),
                'in_progress' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'in-progress')->count(),
                'treated' => $recentOrders->filter(fn ($o) => in_array(strtolower($o->status), ['treated', 'closed']))->count(),
                'total' => $recentOrders->count(),
            ];
        }

        return Inertia::render('ult/index', [
            'isUltDoctor' => $isUltDoctor,
            'recentOrders' => $recentOrders->values(),
            'todayStats' => $todayStats,
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
                'treatmentRecord.attachments',
            ])
            ->findOrFail($id);

        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', 'ULT')
            ->where('id', '!=', $serviceOrder->id)
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('ult/patient', [
            'serviceOrder' => $serviceOrder,
            'previousVisits' => $previousVisits,
            'formConfig' => TreatmentFormConfig::resolve('ULT', $serviceOrder->service?->treatment_form_config),
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (empty($query)) {
            return redirect()->route('ult-dashboard');
        }

        $so = ServiceOrder::query()->where('type', 'ULT')
            ->where(fn ($q) => $q->where('so_number', $query)->orWhere('so_short', $query))
            ->first();

        if ($so) {
            return redirect()->route('ult-patient', ['id' => $so->id]);
        }

        $patient = Patient::query()->where('ps_number', $query)->first();
        if ($patient) {
            $so = ServiceOrder::query()
                ->where('patient_id', $patient->id)->where('type', 'ULT')
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest()->first();
            if ($so) {
                return redirect()->route('ult-patient', ['id' => $so->id]);
            }
        }

        return redirect()->route('ult-dashboard')->with('searchError', "No active Ultrasound order found for \"{$query}\".");
    }
}
