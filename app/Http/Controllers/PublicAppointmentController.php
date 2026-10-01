<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointments\StoreAppointmentRequestRequest;
use App\Models\AppointmentRequest;
use App\Services\AppointmentRequestService;
use App\Services\BookableServices;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicAppointmentController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('public/book-appointment', [
            'serviceGroups' => BookableServices::grouped(),
            'preferredTimes' => AppointmentRequest::PREFERRED_TIMES,
        ]);
    }

    public function store(StoreAppointmentRequestRequest $request, AppointmentRequestService $service): RedirectResponse
    {
        $appointmentRequest = $service->submit($request->requestData(), $request->ip());

        return to_route('public-appointments.submitted', ['reference' => $appointmentRequest->reference]);
    }

    public function submitted(string $reference): Response
    {
        $appointmentRequest = AppointmentRequest::query()->where('reference', $reference)->firstOrFail();

        return Inertia::render('public/appointment-requested', [
            'reference' => $appointmentRequest->reference,
            'status' => $appointmentRequest->status->label(),
            'preferredDate' => $appointmentRequest->preferred_date->toDateString(),
            'preferredTime' => AppointmentRequest::PREFERRED_TIMES[$appointmentRequest->preferred_time] ?? $appointmentRequest->preferred_time,
        ]);
    }
}
