<?php

namespace App\Console\Commands;

use App\Enum\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Console\Command;

class ExpireNoShowAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-no-show-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark today\'s unattended appointments as no-show and clear their draft receivable/reserved queue slot';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Expiring no-show appointments...');

        Appointment::with('receaveable', 'serviceOrder')
            ->where('status', AppointmentStatus::Scheduled)
            ->whereDate('scheduled_at', now()->toDateString())
            ->orderBy('id')
            ->chunkById(200, function ($appointments): void {
                foreach ($appointments as $appointment) {
                    if ($appointment->receaveable && $appointment->receaveable->status === 'draft') {
                        $appointment->receaveable->status = 'cancelled';
                        $appointment->receaveable->save();
                    }

                    if ($appointment->serviceOrder && empty($appointment->serviceOrder->closed_at)) {
                        $appointment->serviceOrder->closed_at = now();
                        $appointment->serviceOrder->status = 'CLOSED';
                        $appointment->serviceOrder->notes = 'Automatically closed by system: appointment no-show.';
                        $appointment->serviceOrder->save();
                    }

                    $appointment->status = AppointmentStatus::NoShow;
                    $appointment->save();
                }

                $this->info('Expired '.count($appointments).' no-show appointments');
            });

        $this->info('Finished expiring no-show appointments.');
    }
}
