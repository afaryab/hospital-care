<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LabController extends Controller
{
    public function index(Request $request): Response
    {
        // Lab has no dedicated profile — all today's lab orders are shown.
        // Access is restricted to authenticated users with any staff profile.
        $user = $request->user();
        $hasAccess = $user->hasAnyProfile();

        $recentOrders = collect();
        $todayStats = ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0];

        if ($hasAccess) {
            $recentOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
                ->where('type', 'PTH')
                ->whereBetween('created_at', DateHelper::todayRangeUtc())
                ->orderByRaw("FIELD(status, 'in-progress', 'IN-PROGRESS', 'open', 'OPEN', 'treated', 'TREATED') ASC")
                ->orderBy('created_at', 'ASC')
                ->limit(50)
                ->get();

            $todayStats = [
                'open' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'open')->count(),
                'in_progress' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'in-progress')->count(),
                'treated' => $recentOrders->filter(fn ($o) => in_array(strtolower($o->status), ['treated', 'closed']))->count(),
                'total' => $recentOrders->count(),
            ];
        }

        return Inertia::render('lab/index', [
            'hasAccess' => $hasAccess,
            'recentOrders' => $recentOrders->values(),
            'todayStats' => $todayStats,
        ]);
    }

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

        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', 'PTH')
            ->where('id', '!=', $serviceOrder->id)
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('lab/patient', [
            'serviceOrder' => $serviceOrder,
            'previousVisits' => $previousVisits,
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (empty($query)) {
            return redirect()->route('lab-dashboard');
        }

        $so = ServiceOrder::query()->where('type', 'PTH')
            ->where(fn ($q) => $q->where('so_number', $query)->orWhere('so_short', $query))
            ->first();

        if ($so) {
            return redirect()->route('lab-patient', ['id' => $so->id]);
        }

        $patient = Patient::query()->where('ps_number', $query)->first();
        if ($patient) {
            $so = ServiceOrder::query()
                ->where('patient_id', $patient->id)->where('type', 'PTH')
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest()->first();
            if ($so) {
                return redirect()->route('lab-patient', ['id' => $so->id]);
            }
        }

        return redirect()->route('lab-dashboard')->with('searchError', "No active Lab order found for \"{$query}\".");
    }
}
