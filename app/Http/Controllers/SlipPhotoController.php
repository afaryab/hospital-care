<?php

namespace App\Http\Controllers;

use App\Enum\SlipPhotoSubject;
use App\Http\Requests\Counter\StoreSlipPhotoRequest;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\SlipPhoto;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SlipPhotoController extends Controller
{
    /**
     * Store a counter webcam photo as the pending slip photo at the
     * receptionist's open counter — for the given patient, or unassigned when
     * snapped before a patient is chosen (claimed on the next patient
     * selected). A retake replaces the previous photo in the same slot
     * (soft-deleted, so it stays auditable).
     */
    public function store(StoreSlipPhotoRequest $request, ?string $year = null, ?string $month = null, ?string $number = null): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $closing = Closing::query()
            ->where('status', 'open')
            ->where('receptionist_id', $request->user()->id)
            ->first();

        abort_if($closing === null, 409, 'Open a counter before capturing slip photos.');

        $patient = $year !== null
            ? Patient::query()->where('ps_number', "PS/{$year}/{$month}/{$number}")->firstOrFail()
            : null;

        $slipPhoto = DB::transaction(function () use ($request, $closing, $patient): SlipPhoto {
            SlipPhoto::query()
                ->when(
                    $patient,
                    fn ($query) => $query->pending($closing->id, $patient->id),
                    fn ($query) => $query->unassigned($closing->id),
                )
                ->get()
                ->each
                ->delete();

            $slipPhoto = SlipPhoto::query()->create([
                'patient_id' => $patient?->id,
                'closing_id' => $closing->id,
                'subject' => $request->validated('subject'),
                'source' => $request->validated('source'),
                'captured_by' => $request->user()->id,
                'captured_at' => now(),
            ]);

            $slipPhoto->addMediaFromRequest('photo')
                ->usingFileName($request->file('photo')->hashName())
                ->toMediaCollection(SlipPhoto::PHOTO_COLLECTION);

            return $slipPhoto;
        });

        activity()
            ->causedBy($request->user())
            ->performedOn($patient ?? $closing)
            ->event('slip_photo_captured')
            ->withProperties([
                'slip_photo_id' => $slipPhoto->id,
                'closing_id' => $closing->id,
                'subject' => $slipPhoto->subject->value,
                'source' => $slipPhoto->source->value,
            ])
            ->log($patient ? 'Slip photo captured' : 'Slip photo captured before patient selection');

        return response()->json(['data' => $slipPhoto->load('capturedBy')->toSummary()], 201);
    }

    /**
     * Relabel a pending photo as patient or guardian.
     */
    public function update(Request $request, SlipPhoto $slipPhoto): JsonResponse
    {
        $this->authorize('update', $slipPhoto);

        $validated = $request->validate([
            'subject' => ['required', Rule::enum(SlipPhotoSubject::class)],
        ]);

        $slipPhoto->update(['subject' => $validated['subject']]);

        activity()
            ->causedBy($request->user())
            ->performedOn($slipPhoto->patient ?? $slipPhoto->closing)
            ->event('slip_photo_relabelled')
            ->withProperties([
                'slip_photo_id' => $slipPhoto->id,
                'subject' => $slipPhoto->subject->value,
            ])
            ->log('Slip photo relabelled');

        return response()->json(['data' => $slipPhoto->load('capturedBy')->toSummary()]);
    }

    public function show(SlipPhoto $slipPhoto): BinaryFileResponse
    {
        $this->authorize('view', $slipPhoto);

        $photo = $slipPhoto->photo();

        abort_if($photo === null, 404);

        return response()->file($photo->getPath(), [
            'Content-Type' => $photo->mime_type,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
