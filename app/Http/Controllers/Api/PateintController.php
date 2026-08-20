<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PiiHasher;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PateintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Patient::query()->orderBy('created_at', 'desc');

            $exactMatches = collect();

            // MR Number — DB-level prefix search on ps_number
            $mrNumber = $request->get('mr_number', false);

            if ($mrNumber) {
                if (Str::length($mrNumber) === 17) {
                    $exactPatient = Patient::where('ps_number', $mrNumber)->first();

                    if ($exactPatient) {
                        $exactMatches->push($exactPatient);
                    }
                }

                $query->where('ps_number', 'LIKE', "{$mrNumber}%");
            }

            // CNIC — hash lookup first; decrypt-scan fallback for legacy rows without hash
            $cnicNumber = $request->get('cnic_number', false);

            if ($cnicNumber && Str::length($cnicNumber) === 15) {
                $normalizedCnic = strtoupper(trim((string) $cnicNumber));
                $cnicHash = PiiHasher::cnic($normalizedCnic);

                $cnicMatches = Patient::where('cnic_hash', $cnicHash)->get();

                // Fallback: scan patients that have cnic but no hash yet (legacy data)
                if ($cnicMatches->isEmpty()) {
                    $legacyMatch = Patient::whereNotNull('cnic')
                        ->where('cnic', '!=', '')
                        ->whereNull('cnic_hash')
                        ->select(['id', 'cnic'])
                        ->get()
                        ->first(fn (Patient $p): bool => strtoupper(trim((string) $p->cnic)) === $normalizedCnic);

                    // Opportunistically backfill the hash so next search is fast
                    if ($legacyMatch) {
                        Patient::where('id', $legacyMatch->id)->update(['cnic_hash' => $cnicHash]);
                        $cnicMatches = collect([$legacyMatch->fresh()]);
                    }
                }

                if ($cnicMatches->isNotEmpty()) {
                    foreach ($cnicMatches as $p) {
                        if (! $exactMatches->contains('id', $p->id)) {
                            $exactMatches->push($p);
                        }
                    }
                    $query->whereIn('id', $cnicMatches->pluck('id')->all());
                } else {
                    $query->whereIn('id', [0]);
                }
            }

            // Name — DB-level LIKE search with prefix-first ordering
            $patientName = $request->get('patient_name', false);

            if ($patientName) {
                $query->where('name', 'LIKE', "%{$patientName}%")
                    ->reorder()
                    ->orderByRaw('CASE WHEN `name` LIKE ? THEN 0 ELSE 1 END', ["{$patientName}%"])
                    ->orderBy('created_at', 'desc');
            }

            // Contact — hash lookup first; decrypt-scan fallback for legacy rows without hash
            // Multiple patients can share the same contact (family members), so collect all matches
            $patientContact = $request->get('patient_contact', false);

            if ($patientContact) {
                $normalizedContact = preg_replace('/\D+/', '', (string) $patientContact) ?: '';

                if ($normalizedContact !== '') {
                    $contactHash = PiiHasher::contact($normalizedContact);
                    $contactMatches = Patient::where('contact_hash', $contactHash)->get();

                    // Fallback: scan patients without contact_hash (legacy data)
                    if ($contactMatches->isEmpty()) {
                        $legacyMatch = Patient::whereNotNull('contact')
                            ->where('contact', '!=', '')
                            ->whereNull('contact_hash')
                            ->select(['id', 'contact'])
                            ->get()
                            ->first(function (Patient $p) use ($normalizedContact): bool {
                                $digits = preg_replace('/\D+/', '', (string) $p->contact) ?: '';

                                return $digits === $normalizedContact;
                            });

                        if ($legacyMatch) {
                            Patient::where('id', $legacyMatch->id)->update(['contact_hash' => $contactHash]);
                            $contactMatches = collect([$legacyMatch->fresh()]);
                        }
                    }

                    if ($contactMatches->isNotEmpty()) {
                        foreach ($contactMatches as $p) {
                            if (! $exactMatches->contains('id', $p->id)) {
                                $exactMatches->push($p);
                            }
                        }
                        $query->whereIn('id', $contactMatches->pluck('id')->all());
                    } else {
                        $query->whereIn('id', [0]);
                    }
                }
            }

            // Gender — DB-level exact match
            $patientGender = $request->get('patient_gender', false);

            if ($patientGender) {
                $query->where('gender', $patientGender);
            }

            // Age — filter by approximate age range (±10 years) using age_days
            $patientAge = $request->get('patient_age', false);

            if ($patientAge && is_numeric($patientAge) && (int) $patientAge > 0) {
                $ageYears = (int) $patientAge;
                $minDays = max(0, ($ageYears - 10) * 365);
                $maxDays = ($ageYears + 10) * 365;
                // Use arithmetic coercion (+0) for cross-database numeric cast of the string column
                $query->whereRaw('(age_days + 0) BETWEEN ? AND ?', [$minDays, $maxDays]);
            }

            // MRI Number — search so_number in service_orders, return associated patient
            $mriNumber = $request->get('mri_number', false);

            if ($mriNumber) {
                $serviceOrders = ServiceOrder::where('so_number', 'LIKE', "{$mriNumber}%")
                    ->with('patient')
                    ->limit(5)
                    ->get();

                foreach ($serviceOrders as $so) {
                    if ($so->patient && ! $exactMatches->contains('id', $so->patient->id)) {
                        $exactMatches->push($so->patient);
                    }
                }
            }

            // FILE Number — search so_short in service_orders, return associated patient
            $fileNumber = $request->get('file_number', false);

            if ($fileNumber) {
                $serviceOrders = ServiceOrder::where('so_short', 'LIKE', "{$fileNumber}%")
                    ->with('patient')
                    ->limit(5)
                    ->get();

                foreach ($serviceOrders as $so) {
                    if ($so->patient && ! $exactMatches->contains('id', $so->patient->id)) {
                        $exactMatches->push($so->patient);
                    }
                }
            }

            if ($exactMatches->isNotEmpty()) {
                $query->whereNotIn('id', $exactMatches->pluck('id')->all());
            }

            $data = [
                'exact' => $exactMatches->values(),
                'possible' => $query->limit(7)->get(),
            ];

            if ($request->wantsJson()) {
                return response()->json(['data' => $data]);
            }

            return Inertia::render('patient', ['patients' => $data]);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while fetching patients.',
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ], 500);
            }

            return back()->withErrors(['error' => 'An error occurred while fetching patients.']);
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
                $cnicHash = PiiHasher::cnic((string) $validated['cnic']);
            }

            $contactHash = PiiHasher::contact((string) $validated['contact']);

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
                        if ($request->wantsJson()) {
                            return response()->json([
                                'message' => 'Existing patient selected',
                                'warning' => true,
                                'used_existing' => true,
                                'data' => $selectedPatient,
                            ]);
                        }

                        return redirect()->route('patients-register-ps-number', $selectedPatient->ps_number_parts);
                    }
                }

                if ($request->wantsJson()) {
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

                return back()->with([
                    'warning' => 'Possible duplicate patient found.',
                    'duplicates' => $duplicates->values(),
                ]);
            }

            if ($request->get('age', false)) {
                // Calculate age in days from age in years
                $birthDate = now()->subYears(intval($request->get('age')));
                $validated['age_days'] = $birthDate->diffInDays(now());
            }

            unset($validated['force_create'], $validated['selected_patient_id']);

            // Normalise nullable encrypted fields — empty string would cause a
            // DecryptException on read because '' is not a valid encrypted payload.
            foreach (['cnic', 'address', 'emergency_contact'] as $nullable) {
                if (isset($validated[$nullable]) && $validated[$nullable] === '') {
                    $validated[$nullable] = null;
                }
            }

            $patient = Patient::create([
                ...$validated,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Patient created successfully',
                    'data' => $patient,
                ], 201);
            }

            return redirect()->route('patients-register-ps-number', $patient->ps_number_parts);

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'An error occurred while creating the patient',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'An error occurred while creating the patient.'])->withInput();
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

        $data = $request->validate([
            'contact' => 'required|string|max:20',
            'age' => 'required|integer|min:0',
            'gender' => 'in:m,f,t',
        ]);

        $patient->contact = $data['contact'];

        $birthDate = now()->subYears(intval($request->get('age')));
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Patient updated successfully',
                'data' => $patient,
            ]);
        }

        return redirect()->route('patients-register-ps-number', $patient->ps_number_parts);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): void {}
}
