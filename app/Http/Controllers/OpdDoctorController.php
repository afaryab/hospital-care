<?php

namespace App\Http\Controllers;

use App\Helpers\DateHelper;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Outpatient doctor workspace. Serves OPD and Peds (PED) — the route passes
 * the department via its `department` default (see routes/web.php).
 */
class OpdDoctorController extends Controller
{
    /**
     * @var array<string, array{label: string, profile: string, route: string, api: string, profileLabel: string}>
     */
    public const DEPARTMENTS = [
        'OPD' => ['label' => 'OPD', 'profile' => 'opdDoctorProfiles', 'route' => 'opd', 'api' => 'opd', 'profileLabel' => 'OPD Doctor'],
        'PED' => ['label' => 'Peds', 'profile' => 'pedDoctorProfiles', 'route' => 'ped', 'api' => 'ped', 'profileLabel' => 'Peds Doctor'],
    ];

    /**
     * Outpatient Doctor Dashboard — shows the doctor's queue for today and a search box.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $type = $this->departmentType($request);
        $department = self::DEPARTMENTS[$type];
        $isOpdDoctor = $user->{$department['profile']}()->exists();

        $recentOrders = collect();
        $todayStats = ['open' => 0, 'in_progress' => 0, 'treated' => 0, 'total' => 0];

        if ($isOpdDoctor) {
            $recentOrders = ServiceOrder::query()
                ->with(['patient:id,name,ps_number,gender,age_days,age_dob', 'service:id,name', 'treatmentRecord:id,service_order_id,is_finalized,diagnosis_text'])
                ->where('type', $type)
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
            'searchPrefill' => ServiceOrder::latestSoShortPrefix([$type], $type),
            'department' => $this->departmentProps($type),
        ]);
    }

    /**
     * Open a specific service order for prescription/treatment.
     */
    public function show(Request $request, int $id): Response
    {
        $type = $this->departmentType($request);

        $serviceOrder = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number,gender,age_days,age_dob,contact',
                'service:id,name,service_department_id',
                'doctor:id,name',
                'treatmentRecord.vitalSigns',
            ])
            ->findOrFail($id);

        // Load previous visits to this department for this patient (last 10)
        $previousVisits = ServiceOrder::query()
            ->with(['treatmentRecord:id,service_order_id,diagnosis_text,chief_complaint,treated_at,is_finalized'])
            ->where('patient_id', $serviceOrder->patient_id)
            ->where('type', $type)
            ->where('id', '!=', $serviceOrder->id)
            ->whereNotNull('status')
            ->latest('created_at')
            ->limit(10)
            ->get(['id', 'so_number', 'status', 'created_at', 'patient_id']);

        return Inertia::render('opd/patient', [
            'serviceOrder' => $serviceOrder,
            'previousVisits' => $previousVisits,
            'department' => $this->departmentProps($type),
        ]);
    }

    /**
     * Search for a service order or patient by number — used from the dashboard search bar.
     * Redirects to the correct page.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        $type = $this->departmentType($request);
        $route = self::DEPARTMENTS[$type]['route'];

        if (empty($query)) {
            return redirect()->route("{$route}-dashboard");
        }

        // Try exact SO number match first
        $so = ServiceOrder::query()
            ->where('so_number', $query)
            ->orWhere('so_short', $query)
            ->first();

        if ($so) {
            return redirect()->route("{$route}-patient", ['id' => $so->id]);
        }

        // Try patient PS number
        $patient = Patient::query()->where('ps_number', $query)->first();

        if ($patient) {
            // Find the most recent service order in this department for this patient
            $so = ServiceOrder::query()
                ->where('patient_id', $patient->id)
                ->where('type', $type)
                ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS'])
                ->latest('created_at')
                ->first();

            if ($so) {
                return redirect()->route("{$route}-patient", ['id' => $so->id]);
            }
        }

        $label = self::DEPARTMENTS[$type]['label'];

        return redirect()->route("{$route}-dashboard")->with('searchError', "No active {$label} service order found for \"{$query}\".");
    }

    private function departmentType(Request $request): string
    {
        $type = strtoupper((string) ($request->route('department') ?? 'OPD'));

        return array_key_exists($type, self::DEPARTMENTS) ? $type : 'OPD';
    }

    /**
     * URLs and labels the shared outpatient pages need for this department.
     *
     * @return array<string, string>
     */
    private function departmentProps(string $type): array
    {
        $department = self::DEPARTMENTS[$type];
        $route = $department['route'];
        $api = $department['api'];

        return [
            'type' => $type,
            'label' => $department['label'],
            'profileLabel' => $department['profileLabel'],
            'dashboardUrl' => route("{$route}-dashboard", absolute: false),
            'patientUrlTemplate' => route("{$route}-patient", ['id' => '__ID__'], false),
            'apiMyQueueUrl' => route("api-{$api}-my-queue", absolute: false),
            'apiSearchUrl' => route("api-{$api}-search", absolute: false),
            'apiSaveTreatmentUrlTemplate' => route("api-{$api}-save-treatment", ['serviceOrder' => '__ID__'], false),
            'apiStatusUrlTemplate' => route("api-{$api}-update-status", ['serviceOrder' => '__ID__'], false),
        ];
    }
}
