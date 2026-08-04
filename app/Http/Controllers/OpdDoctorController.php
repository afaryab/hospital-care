<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OpdDoctorController extends Controller
{
    /**
     * OPD Doctor Dashboard — shows the doctor's queue for today and a search box.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isOpdDoctor = $user->opdDoctorProfiles()->exists();

        $recentOrders = collect();
        $todayStats = ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0];

        if ($isOpdDoctor) {
            $recentOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
                ->where('type', 'OPD')
                ->where('doctor_id', $user->id)
                ->whereBetween('created_at', DateHelper::todayRangeUtc())
                ->orderByRaw("CASE WHEN LOWER(status) = 'in-progress' THEN 0 WHEN LOWER(status) = 'open' THEN 1 WHEN LOWER(status) = 'treated' THEN 2 ELSE 3 END ASC")
                ->orderBy('created_at', 'ASC')
                ->limit(30)
                ->get();

            $todayStats = [
                'open' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'open')->count(),
                'in_progress' => $recentOrders->filter(fn ($o) => strtolower($o->status) === 'in-progress')->count(),
                'treated' => $recentOrders->filter(fn ($o) => in_array(strtolower($o->status), ['treated', 'closed']))->count(),
                'total' => $recentOrders->count(),
            ];
        }

        return Inertia::render('opd/index', [
            'isOpdDoctor' => $isOpdDoctor,
            'recentOrders' => $recentOrders->values(),
            'todayStats' => $todayStats,
            'searchPrefill' => ServiceOrder::latestSoShortPrefix(['OPD'], 'OPD'),
        ]);
    }

    /**
     * Open a specific service order for prescription/treatment.
     */
    public function show(Request $request, int $id): Response
    {
        $user = $request->user();

        $serviceOrder = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number,gender,age_days,age_dob,contact',
                'service:id,name,service_department_id',
                'doctor:id,name',
                'treatmentRecord.vitalSigns',
            ])
            ->findOrFail($id);

        // Load previous OPD visits for this patient (last 10)
        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', 'OPD')
            ->where('id', '!=', $serviceOrder->id)
            ->whereNotNull('status')
            ->latest('created_at')
            ->limit(10)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('opd/patient', [
            'serviceOrder' => $serviceOrder,
            'previousVisits' => $previousVisits,
        ]);
    }

    /**
     * Search for a service order or patient by number — used from the dashboard search bar.
     * Redirects to the correct page.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (empty($query)) {
            return redirect()->route('opd-dashboard');
        }

        // Try exact SO number match first
        $so = ServiceOrder::query()
            ->where('so_number', $query)
            ->orWhere('so_short', $query)
            ->first();

        if ($so) {
            return redirect()->route('opd-patient', ['id' => $so->id]);
        }

        // Try patient PS number
        $patient = Patient::query()->where('ps_number', $query)->first();

        if ($patient) {
            // Find the most recent OPD service order for this patient
            $so = ServiceOrder::query()
                ->where('patient_id', $patient->id)
                ->where('type', 'OPD')
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest('created_at')
                ->first();

            if ($so) {
                return redirect()->route('opd-patient', ['id' => $so->id]);
            }
        }

        return redirect()->route('opd-dashboard')->with('searchError', "No active OPD service order found for \"{$query}\".");
    }
}
