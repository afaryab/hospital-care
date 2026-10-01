<?php

namespace App\Services;

use App\Enum\AppointmentRequestStatus;
use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentRequestService
{
    public function __construct(private AppointmentService $appointments) {}

    /**
     * @param  array{name: string, contact: string, gender?: string|null, age_years?: int|null, service_id?: int|null, preferred_date: string, preferred_time: string, notes?: string|null}  $data
     */
    public function submit(array $data, ?string $ipAddress = null): AppointmentRequest
    {
        return AppointmentRequest::create([
            ...$data,
            'status' => AppointmentRequestStatus::Pending,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Book the real appointment, against an existing patient or a newly
     * registered one built from the request.
     *
     * @param  array{patient_id?: int|null, service_id: int, doctor_id?: int|null, scheduled_at: string, notes?: string|null}  $data
     */
    public function confirm(AppointmentRequest $request, User $user, array $data): AppointmentRequest
    {
        return DB::transaction(function () use ($request, $user, $data): AppointmentRequest {
            $request = AppointmentRequest::query()->lockForUpdate()->findOrFail($request->id);

            if ($request->status !== AppointmentRequestStatus::Pending) {
                throw ValidationException::withMessages(['request' => 'This request has already been '.$request->status->value.'.']);
            }

            $patient = ! empty($data['patient_id'])
                ? Patient::query()->findOrFail($data['patient_id'])
                : Patient::create([
                    'name' => $request->name,
                    'contact' => $request->contact,
                    'gender' => $request->gender ?? 'o',
                    'age_days' => $request->age_years !== null ? (int) now()->subYears($request->age_years)->diffInDays(now()) : null,
                ]);

            $appointment = $this->appointments->book([
                'patient_id' => $patient->id,
                'service_id' => $data['service_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'scheduled_at' => $data['scheduled_at'],
                'notes' => $data['notes'] ?? $request->notes,
                'created_by' => $user->id,
            ]);

            $request->update([
                'status' => AppointmentRequestStatus::Confirmed,
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'handled_by' => $user->id,
                'handled_at' => now(),
            ]);

            return $request;
        });
    }

    public function reject(AppointmentRequest $request, User $user, string $reason): AppointmentRequest
    {
        if ($request->status !== AppointmentRequestStatus::Pending) {
            throw ValidationException::withMessages(['request' => 'This request has already been '.$request->status->value.'.']);
        }

        $request->update([
            'status' => AppointmentRequestStatus::Rejected,
            'rejection_reason' => $reason,
            'handled_by' => $user->id,
            'handled_at' => now(),
        ]);

        return $request;
    }
}
