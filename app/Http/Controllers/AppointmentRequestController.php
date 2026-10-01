<?php

namespace App\Http\Controllers;

use App\Enum\AppointmentRequestStatus;
use App\Http\Requests\Appointments\ConfirmAppointmentRequestRequest;
use App\Http\Requests\Appointments\RejectAppointmentRequestRequest;
use App\Models\AppointmentRequest;
use App\Services\AppointmentRequestService;
use App\Services\BookableServices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', AppointmentRequest::class);

        $status = $request->string('status', AppointmentRequestStatus::Pending->value)->toString();

        $requests = AppointmentRequest::query()
            ->with(['service:id,name', 'patient:id,name,ps_number', 'appointment:id,appointment_number,scheduled_at', 'handler:id,name'])
            ->when(in_array($status, array_column(AppointmentRequestStatus::cases(), 'value'), true), fn ($q) => $q->where('status', $status))
            ->orderBy('preferred_date')
            ->oldest('id')
            ->paginate(20)
            ->withQueryString();

        $requests->getCollection()->transform(fn (AppointmentRequest $item): array => [
            ...$item->only(['id', 'name', 'contact', 'gender', 'age_years', 'service_id', 'preferred_time', 'notes', 'rejection_reason']),
            'status' => $item->status->value,
            'preferred_date' => $item->preferred_date->toDateString(),
            'preferred_time_label' => AppointmentRequest::PREFERRED_TIMES[$item->preferred_time] ?? $item->preferred_time,
            'created_at' => $item->created_at?->toIso8601String(),
            'service' => $item->service?->only(['id', 'name']),
            'patient' => $item->patient?->only(['id', 'name', 'ps_number']),
            'appointment' => $item->appointment?->only(['appointment_number', 'scheduled_at']),
            'handler' => $item->handler?->only(['name']),
            'matching_patients' => $item->status === AppointmentRequestStatus::Pending
                ? $item->matchingPatients()->map(fn ($patient): array => $patient->only(['id', 'name', 'ps_number', 'gender', 'age']))->all()
                : [],
        ]);

        return Inertia::render('appointments/requests', [
            'requests' => $requests,
            'status' => $status,
            'serviceGroups' => BookableServices::grouped(),
            'pendingCount' => AppointmentRequest::query()->where('status', AppointmentRequestStatus::Pending)->count(),
        ]);
    }

    public function confirm(ConfirmAppointmentRequestRequest $request, AppointmentRequest $appointmentRequest, AppointmentRequestService $service): RedirectResponse
    {
        $service->confirm($appointmentRequest, $request->user(), $request->validated());

        return back()->with('success', 'Appointment booked.');
    }

    public function reject(RejectAppointmentRequestRequest $request, AppointmentRequest $appointmentRequest, AppointmentRequestService $service): RedirectResponse
    {
        $service->reject($appointmentRequest, $request->user(), $request->validated('rejection_reason'));

        return back()->with('success', 'Request rejected.');
    }
}
