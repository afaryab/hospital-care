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
        try{
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

            if(count($exactMatches) > 0){
                $query->whereNotIn('id', array_map(function($item){
                    return $item;
                }, collect($exactMatches)->pluck('id')->toArray()));
            }


            return response()->json([
                "data" => [
                    "exact" => $exactMatches,
                    "possible" => $query->limit(3)->get()
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([
                "message" => "An error occurred while fetching patients.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'cnic' => 'nullable|string|size:15|unique:patients,cnic',
                'contact' => 'required|string|max:20',
                'gender' => 'required|in:m,f,t',
                'date_of_birth' => 'nullable|date',
                'address' => 'nullable|string|max:500',
                'emergency_contact' => 'nullable|string|max:20',
                'blood_group' => 'nullable|string|max:5',
            ]);

            if($request->get('age', false)){
                // Calculate age in days from age in years
                $birthDate = now()->subYears(intval($request->get('age')));
                $validated['date_of_birth'] = $birthDate->toDateString();
            }



            $patient = Patient::create([
                ...$validated
            ]);

            return response()->json([
                'message' => 'Patient created successfully',
                'data' => $patient
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the patient',
                'error' => $e->getMessage()
            ], 500);
        }
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
