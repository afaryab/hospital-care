<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PateintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::query()->orderBy('created_at','DESC')->where('id','!=', null);

        $exactMatches = [];

        // Check if the request have MR Number
        $mrNumber = $request->get('mr_number', false);

        if($mrNumber){
            if(Str::length($mrNumber) === 17){
                $exactMatches[] = Patient::where(['ps_number' => $mrNumber])->first();
            }
            
            $query->where('ps_number', 'LIKE', "{$mrNumber}%");
        }


        
        $cnicNumber = $request->get('cnic_number', false);

        if($cnicNumber){
            if(Str::length($mrNumber) === 17){
                $exactMatches[] = Patient::where(['cnic' => $mrNumber])->first();
            }
            
            $query->where('cnic', 'LIKE', "{$mrNumber}%");
        }

        $patientName = $request->get('patient_name', false);

        if($patientName){
            $query->where(function ($query) use ($patientName) {
                $query->where('name', 'LIKE', "{$patientName}%")
                      ->orWhere('name', 'LIKE', "{$patientName}%")
                      ->orWhere('name', 'LIKE', "%{$patientName}");
            });
        }

        $patientContact = $request->get('patient_contact', false);

        if($patientContact){
            $query->where('contact', 'LIKE', "{$patientContact}%");
        }

        $patientGender = $request->get('patient_gender', false);

        if($patientGender){
            $query->where('gender', $patientGender);
        }
        return response()->json([
            "data" => [
                "exact" => $exactMatches,
                "possible" => $query->limit(3)->get()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
