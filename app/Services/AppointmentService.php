<?php

namespace App\Services;

use App\Enum\AppointmentPriorityMode;
use App\Enum\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Receaveable;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Book a future appointment. The current global priority mode is
     * snapshotted onto the appointment so a later change to the hospital-wide
     * setting never retroactively changes how an already-booked appointment
     * behaves.
     *
     * No ServiceOrder is created here for any mode — the live queue has no
     * date scoping, so materializing immediately would make a visit booked
     * weeks out appear in *today's* queue. That happens on the appointment's
     * actual day instead, via the app:materialize-appointments command.
     *
     * @param  array{patient_id:int,service_id:int,doctor_id?:int|null,scheduled_at:string,notes?:string|null,created_by:int}  $data
     */
    public function book(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $priorityMode = AppointmentPriorityMode::current();

            $appointment = Appointment::create([
                'patient_id' => $data['patient_id'],
                'service_id' => $data['service_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'scheduled_at' => $data['scheduled_at'],
                'priority_mode' => $priorityMode,
                'status' => AppointmentStatus::Scheduled,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'],
            ]);

            if ($priorityMode === AppointmentPriorityMode::Priority) {
                $service = Service::findOrFail($data['service_id']);

                $receaveable = Receaveable::create([
                    'patient_id' => $data['patient_id'],
                    'appointment_id' => $appointment->id,
                    'amount' => $service->charges,
                    'orignal_amount' => $service->charges,
                    'status' => 'draft',
                    'due_date' => Carbon::parse($data['scheduled_at'])->toDateString(),
                ]);

                $appointment->receaveable_id = $receaveable->id;
                $appointment->save();
            }

            return $appointment;
        });
    }

    /**
     * Cancel a scheduled appointment before its day arrives (or before
     * check-in). Mirrors the cleanup ExpireNoShowAppointments performs for
     * unattended appointments: the draft receivable is cancelled (never
     * deleted, to preserve the audit trail) and any already-materialized
     * reserved service order is closed so it drops out of the live queue.
     */
    public function cancel(Appointment $appointment): Appointment
    {
        return DB::transaction(function () use ($appointment) {
            $appointment->loadMissing(['receaveable', 'serviceOrder']);

            if ($appointment->receaveable && $appointment->receaveable->status === 'draft') {
                $appointment->receaveable->status = 'cancelled';
                $appointment->receaveable->save();
            }

            if ($appointment->serviceOrder && empty($appointment->serviceOrder->closed_at)) {
                $appointment->serviceOrder->closed_at = now();
                $appointment->serviceOrder->status = 'CLOSED';
                $appointment->serviceOrder->notes = 'Automatically closed by system: appointment was cancelled.';
                $appointment->serviceOrder->save();
            }

            $appointment->status = AppointmentStatus::Cancelled;
            $appointment->cancelled_at = now();
            $appointment->save();

            return $appointment;
        });
    }
}
