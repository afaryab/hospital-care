<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\StoreAppointmentRequestRequest;
use App\Models\AppointmentRequest;
use App\Services\AppointmentRequestService;
use App\Services\BookableServices;
use Illuminate\Http\JsonResponse;

/**
 * Unauthenticated booking API for the hospital website or mobile apps.
 * Responses never include patient data — only the request's own reference
 * and status.
 */
class PublicAppointmentRequestController extends Controller
{
    public function services(): JsonResponse
    {
        return response()->json([
            'data' => BookableServices::grouped(),
            'meta' => ['preferred_times' => AppointmentRequest::PREFERRED_TIMES],
        ]);
    }

    public function store(StoreAppointmentRequestRequest $request, AppointmentRequestService $service): JsonResponse
    {
        $appointmentRequest = $service->submit($request->requestData(), $request->ip());

        return response()->json(['data' => $this->present($appointmentRequest)], 201);
    }

    public function show(string $reference): JsonResponse
    {
        $appointmentRequest = AppointmentRequest::query()->where('reference', $reference)->firstOrFail();

        return response()->json(['data' => $this->present($appointmentRequest)]);
    }

    /**
     * @return array{reference: string, status: string, preferred_date: string, preferred_time: string, scheduled_at: string|null}
     */
    private function present(AppointmentRequest $appointmentRequest): array
    {
        return [
            'reference' => $appointmentRequest->reference,
            'status' => $appointmentRequest->status->value,
            'preferred_date' => $appointmentRequest->preferred_date->toDateString(),
            'preferred_time' => $appointmentRequest->preferred_time,
            'scheduled_at' => $appointmentRequest->appointment?->scheduled_at?->toIso8601String(),
        ];
    }
}
