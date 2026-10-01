<?php

namespace App\Http\Controllers;

use App\Http\Requests\Patients\StorePatientPhotoRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientPhotoController extends Controller
{
    public function store(StorePatientPhotoRequest $request, string $year, string $month, string $number): RedirectResponse
    {
        $patient = $this->resolvePatient($year, $month, $number);

        $this->authorize('update', $patient);

        $previous = $patient->currentPhoto();

        $media = $patient->addMediaFromRequest('photo')
            ->usingFileName($request->file('photo')->hashName())
            ->withCustomProperties([
                'source' => $request->validated('source'),
                'uploaded_by' => $request->user()->id,
            ])
            ->toMediaCollection(Patient::PHOTOS_COLLECTION);

        activity()
            ->causedBy($request->user())
            ->performedOn($patient)
            ->event('photo_updated')
            ->withProperties([
                'source' => $request->validated('source'),
                'media_id' => $media->id,
                'previous_media_id' => $previous?->id,
            ])
            ->log('Patient photo updated');

        return back()->with('success', 'Patient photo saved.');
    }

    public function show(Request $request, string $year, string $month, string $number): BinaryFileResponse
    {
        $patient = $this->resolvePatient($year, $month, $number);

        $this->authorize('view', $patient);

        $photo = $patient->currentPhoto();

        abort_if($photo === null, 404);

        return response()->file($photo->getPath(), [
            'Content-Type' => $photo->mime_type,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    private function resolvePatient(string $year, string $month, string $number): Patient
    {
        return Patient::query()->where('ps_number', "PS/{$year}/{$month}/{$number}")->firstOrFail();
    }
}
