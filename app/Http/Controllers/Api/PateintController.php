<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PateintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Patient::query()->orderBy('created_at', 'DESC')->where('id', '!=', null);

            $exactMatches = collect();

            // Check if the request have MR Number
            $mrNumber = $request->get('mr_number', false);

            if ($mrNumber) {
                if (Str::length($mrNumber) === 17) {
                    $exactPatient = Patient::where(['ps_number' => $mrNumber])->first();

                    if ($exactPatient) {
                        $exactMatches->push($exactPatient);
                    }
                }

                $query->where('ps_number', 'LIKE', "{$mrNumber}%");
            }

            $cnicNumber = $request->get('cnic_number', false);

            if ($cnicNumber) {
                $normalizedCnic = strtoupper(trim((string) $cnicNumber));

                if (Str::length($cnicNumber) === 15) {
                    $exactPatient = Patient::where('cnic_hash', hash('sha256', $normalizedCnic))->first();

                    if ($exactPatient) {
                        $exactMatches->push($exactPatient);
                    }
                }

                $cnicMatchIds = Patient::query()->select(['id', 'cnic'])->get()
                    ->filter(function (Patient $patient) use ($normalizedCnic): bool {
                        $patientCnic = strtoupper(trim((string) $patient->cnic));

                        return $patientCnic !== '' && str_starts_with($patientCnic, $normalizedCnic);
                    })
                    ->pluck('id');

                $query->whereIn('id', $cnicMatchIds->isNotEmpty() ? $cnicMatchIds->all() : [0]);
            }

            $patientName = $request->get('patient_name', false);

            if ($patientName) {
                $query->where(function ($query) use ($patientName) {
                    $query->where('name', 'LIKE', "{$patientName}%")
                        ->orWhere('name', 'LIKE', "%{$patientName}%")
                        ->orWhere('name', 'LIKE', "%{$patientName}");
                });
            }

            $patientContact = $request->get('patient_contact', false);

            if ($patientContact) {
                $normalizedContact = preg_replace('/\D+/', '', (string) $patientContact) ?: '';

                $contactMatchIds = Patient::query()->select(['id', 'contact'])->get()
                    ->filter(function (Patient $patient) use ($patientContact, $normalizedContact): bool {
                        $contact = (string) $patient->contact;
                        $contactDigits = preg_replace('/\D+/', '', $contact) ?: '';

                        return str_starts_with($contact, (string) $patientContact)
                            || ($normalizedContact !== '' && str_starts_with($contactDigits, $normalizedContact));
                    })
                    ->pluck('id');

                $query->whereIn('id', $contactMatchIds->isNotEmpty() ? $contactMatchIds->all() : [0]);
            }

            $patientGender = $request->get('patient_gender', false);

            if ($patientGender) {
                $query->where('gender', $patientGender);
            }

            if ($exactMatches->isNotEmpty()) {
                $query->whereNotIn('id', $exactMatches->pluck('id')->all());
            }

            return response()->json([
                'data' => [
                    'exact' => $exactMatches->values(),
                    'possible' => $query->limit(7)->get(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching patients.',
                'error' => $e->getMessage(),
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
                'cnic' => 'nullable|string|size:15',
                'contact' => 'required|string|max:20',
                'gender' => 'required|in:m,f,t',
                'date_of_birth' => 'nullable|date',
                'address' => 'nullable|string|max:500',
                'emergency_contact' => 'nullable|string|max:20',
                'blood_group' => 'nullable|string|max:5',
                'force_create' => 'nullable|boolean',
                'selected_patient_id' => 'nullable|integer|exists:patients,id',
            ]);

            $cnicHash = null;
            if (! empty($validated['cnic'])) {
                $cnicHash = hash('sha256', strtoupper(trim((string) $validated['cnic'])));
            }

            $contactHash = hash('sha256', preg_replace('/\D+/', '', (string) $validated['contact']) ?: '');

            $duplicatesQuery = Patient::query()->where(function ($query) use ($cnicHash, $contactHash) {
                if (! empty($cnicHash)) {
                    $query->orWhere('cnic_hash', $cnicHash);
                }

                $query->orWhere('contact_hash', $contactHash);
            });

            $duplicates = $duplicatesQuery->limit(10)->get();

            if ($duplicates->isNotEmpty() && ! $request->boolean('force_create')) {
                $selectedPatientId = $request->integer('selected_patient_id');

                if ($selectedPatientId) {
                    $selectedPatient = $duplicates->firstWhere('id', $selectedPatientId);

                    if ($selectedPatient) {
                        return response()->json([
                            'message' => 'Existing patient selected',
                            'warning' => true,
                            'used_existing' => true,
                            'data' => $selectedPatient,
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Possible duplicate patient found',
                    'warning' => true,
                    'can_proceed' => true,
                    'data' => [
                        'exact' => $duplicates->take(3)->values(),
                        'possible' => $duplicates->values(),
                    ],
                ], 409);
            }

            if ($request->get('age', false)) {
                // Calculate age in days from age in years
                $birthDate = now()->subYears(intval($request->get('age')));
                $validated['age_days'] = $birthDate->diffInDays(now());
            }

            unset($validated['force_create'], $validated['selected_patient_id']);

            $patient = Patient::create([
                ...$validated,
            ]);

            return response()->json([
                'message' => 'Patient created successfully',
                'data' => $patient,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the patient',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $patient = Patient::findOrFail($id);
        // dd($request->all());

        $data = $request->validate([
            // 'cnic' => 'nullable|string|size:15|unique:patients,cnic,'.$patient->id,
            'contact' => 'required|string|max:20',
            'age' => 'required|integer|min:0',
            // 'address' => 'nullable|string|max:500',
            // 'emergency_contact' => 'nullable|string|max:20',
            // 'blood_group' => 'nullable|string|max:5',
            'gender' => 'in:m,f,t',
        ]);

        $patient->contact = $data['contact'];

        $birthDate = now()->subYears(intval($request->get('age')));
        // Get age in days

        $patient->age_days = $birthDate->diffInDays(now());

        if ($request->get('cnic', false)) {
            $patient->cnic = $request->get('cnic');
        }

        if ($request->get('address', false)) {
            $patient->address = $request->get('address');
        }

        if ($request->get('emergency_contact', false)) {
            $patient->emergency_contact = $request->get('emergency_contact');
        }

        if ($request->get('blood_group', false)) {
            $patient->blood_group = $request->get('blood_group');
        }

        if ($request->get('gender', false)) {
            $patient->gender = $request->get('gender');
        }

        $patient->save();

        return response()->json([
            'message' => 'Patient updated successfully',
            'data' => $patient,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
