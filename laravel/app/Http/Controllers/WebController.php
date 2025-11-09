<?php

namespace App\Http\Controllers;

use App\CounterStatus;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\ServiceDepartment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profiles = collect()->merge([
            'admin' => $user->adminProfiles()->get(),
            $user->accountantProfiles()->get(),
            $user->receptionistProfiles()->get(),
            $user->opdDoctorProfiles()->get(),
            $user->indDoctorProfiles()->get(),
            $user->emergencyDoctorProfiles()->get(),
            $user->dentistProfiles()->get(),
            $user->ultrasoundDoctorProfiles()->get(),
            $user->xrayTechnicianProfiles()->get(),
            $user->nursingStaffProfiles()->get(),
        ]);

        return Inertia::render('dashboard');
    }


    public function register($year = false, $month = false){

        $query = Patient::query();

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->paginate(8);

        $serviceDepartments = ServiceDepartment::all();

        return Inertia::render('register',[
            'yearSelected' => $year,
            'monthSelected' => $month,
            'patientsPaginated' => $data,
            'serviceDepartments' => $serviceDepartments
        ]);


    }


    public function patient($year, $month, $number, $departmentKey = false){

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::where('ps_number', $psNumber)->firstOrFail();

        $serviceDepartments = ServiceDepartment::all();

        return Inertia::render('patient',[
            'departmentKey' => $departmentKey,
            'patientData' => $patientData,
            'serviceDepartments' => $serviceDepartments
        ]);

        
    }


    public function treatment($year, $month, $number, $departmentKey, $treatment){

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::where('ps_number', $psNumber)->firstOrFail();

        return Inertia::render('patient',[
            'departmentKey' => $departmentKey,
            'treatmentKey' => $treatment,
            'patientData' => $patientData,
        ]);

    }

    public function counter(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{
            return redirect(route('counter-view',[
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number
            ]));
        }
    }

    public function countersList($year = false, $month = false)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', request()->user()->id)->first();

        $query = Closing::query();

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->paginate(8);

        return Inertia::render('counter/list',[
            'yearSelected' => $year,
            'monthSelected' => $month,
            'closings' => $data,
            'openCounter' => $openCounter
        ]);
    }

    public function counterOpen(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();
        
        if($openCounter){
            return redirect(route('counter-view',[
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number
            ]));
        }else{

            return Inertia::render('counter/open',[
                'recptions' => Reception::all()
            ]);
        }
    }

    public function counterStore(Request $request)
    {

        $request->validate([
            'opening_balance' => 'required|numeric',
            'reception_id' => 'required|exists:receptions,id'
        ]);

        $counter = Closing::create([
            'reception_id' => $request->reception_id,
            'receptionist_id' => $request->user()->id,
            'ct_number' => Closing::generateCounterNumber(),
            'status' => CounterStatus::OPEN,
            'opening_amount' => $request->opening_balance
        ]);

        return redirect(route('counter-view',[
            'ctYear' => $counter->year,
            'ctMonth' => $counter->month,
            'ctNumber' => $counter->number
        ]));
    }

    public function counterClose(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();
        
        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{
            return Inertia::render('counter/close',[
                'openCounter' => $openCounter
            ]);
        }
    
    }

    public function counterView($ctYear = '', $ctMonth = '', $ctNumber = '', Request $request)
    {

        $ctNumber = 'CT/'.$ctYear.'/'.$ctMonth.'/'.$ctNumber;

        $openCounter = Closing::with('transactions')->where('ct_number',$ctNumber)->first();

        //->where('receptionist_id', $request->user()->id)

        // dd($openCounter);

        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{
            return Inertia::render('counter/view',[
                'openCounter' => $openCounter
            ]);
        }
    }

    public function counterPatient($pYear = false, $pMonth = false, $number = false, $departmentKey = false)
    {
        $openCounter = Closing::with('transactions')->where('status','open')->where('receptionist_id', request()->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }
        $pageData = [
            'openCounter' => $openCounter
        ];

        if($pYear || $pMonth || $number){

            $psNumber = 'PS/'.$pYear.'/'.$pMonth.'/'.$number;

            $patientData = Patient::where('ps_number', $psNumber)->firstOrFail();

            $pageData['selectedPatient'] = $patientData;
        }

        $pageData['departments'] = ServiceDepartment::all();
        $pageData['departmentKey'] = $departmentKey;
        

        return Inertia::render('counter/patient',$pageData);
    }

    public function hospitalSettings()
    {
        return Inertia::render('admin/hospital-settings');
    }
}
