<?php

namespace App\Observers;

use App\Enum\AppointmentPriorityMode;
use App\Models\Appointment;

class AppointmentObserver
{
    public function creating(Appointment $appointment): void
    {
        if (empty($appointment->appointment_number)) {
            $appointment->appointment_number = Appointment::generateAppointmentNumber();
        }

        if (empty($appointment->priority_mode)) {
            $appointment->priority_mode = AppointmentPriorityMode::current();
        }
    }
}
