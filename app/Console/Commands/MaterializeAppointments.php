<?php

namespace App\Console\Commands;

use App\Enum\AppointmentPriorityMode;
use App\Enum\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\ServiceOrder;
use Illuminate\Console\Command;

class MaterializeAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:materialize-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the reserved queue slot for today\'s Priority/Medium appointments';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Materializing today\'s appointments into the live queue...');

        Appointment::with('patient', 'service.department')
            ->where('status', AppointmentStatus::Scheduled)
            ->whereNull('service_order_id')
            ->whereDate('scheduled_at', now()->toDateString())
            ->where('priority_mode', '!=', AppointmentPriorityMode::Standard->value)
            ->orderBy('id')
            ->chunkById(200, function ($appointments): void {
                foreach ($appointments as $appointment) {
                    $patient = $appointment->patient;
                    $service = $appointment->service;

                    if (! $patient || ! $service || ! $service->department) {
                        continue;
                    }

                    $type = $service->department->slug;

                    $s = ServiceOrder::generateServiceOrderNumber($type);
                    $ss = ServiceOrder::generateShortServiceOrderNumber($type);

                    $soShort = $type.'/'.str_pad((string) $ss, 8, '0', STR_PAD_LEFT);
                    $soNumber = $patient->ps_number.'/'.$type.'/'.str_pad((string) $s, 8, '0', STR_PAD_LEFT);

                    $token = ServiceOrder::generateToken($appointment->doctor_id, $service->id);

                    $order = ServiceOrder::create([
                        'type' => $type,
                        'token' => $token,
                        'so_number' => $soNumber,
                        'so_short' => $soShort,
                        'created_by' => $appointment->created_by,
                        'patient_id' => $patient->id,
                        'service_id' => $service->id,
                        'doctor_id' => $appointment->doctor_id,
                        'appointment_id' => $appointment->id,
                        'priority' => $appointment->priority_mode === AppointmentPriorityMode::Priority ? 1 : 0,
                        'is_composit' => $service->is_composit_service,
                        'notes' => "Reserved for appointment {$appointment->appointment_number}",
                        'notes_json' => [],
                        'payee_type' => Patient::class,
                        'payee_id' => $patient->id,
                    ]);

                    // 'status' is intentionally not mass-assignable (see
                    // ServiceOrder::$fillable) — set directly, same as
                    // CloseOldServiceOrders does when transitioning status.
                    $order->status = 'reserved';
                    $order->save();

                    $appointment->service_order_id = $order->id;
                    $appointment->save();
                }

                $this->info('Materialized '.count($appointments).' appointments');
            });

        $this->info('Finished materializing appointments.');
    }
}
