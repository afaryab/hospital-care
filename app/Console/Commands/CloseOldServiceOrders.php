<?php

namespace App\Console\Commands;

use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\TreatmentRecord;
use App\Models\Triage;
use App\Models\TriageHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CloseOldServiceOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-old-service-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close old service orders for existing transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Closing existing service orders for transactions...');

        ServiceOrder::whereNull('closed_at')
            // Created 12 hours ago or earlier
            ->where('created_at', '<=', now()->subMinute(5))
            ->where('status', 'open')
            ->where('type', 'OPD')
            ->orderBy('id')
            ->chunk(2000, function ($orders) {
                foreach ($orders as $order) {
                    $order->closed_at = now();
                    $order->status = 'CLOSED';
                    $order->notes = 'Automatically closed by system due to being open for more than 5 minutes.';
                    $order->save();
                }
                $this->info('Processed '.count($orders).' service orders');
            });

        // EMG: close mean discharge. A doctor who has already started/saved a
        // treatment record owns the discharge from here — auto-close only
        // applies to orders nobody ever attended to (no treatment record at
        // all), and gives those a default "green" (Non Urgent) triage so
        // Triage Dashboard reporting isn't left with a gap.
        $greenTriageId = Triage::where('color', 'green')->where('is_active', true)->value('id');
        $emgDepartmentId = ServiceDepartment::where('slug', 'EMG')->value('id');

        ServiceOrder::whereNull('closed_at')
            // Created 12 hours ago or earlier
            ->where('created_at', '<=', now()->subHours(12))
            ->where('status', 'open')
            ->where('type', 'EMG')
            ->whereDoesntHave('treatmentRecord')
            ->orderBy('id')
            ->chunk(2000, function ($orders) use ($greenTriageId, $emgDepartmentId) {
                foreach ($orders as $order) {
                    $actorId = $order->doctor_id ?? $order->created_by;

                    $treatmentRecord = TreatmentRecord::create([
                        'service_order_id' => $order->id,
                        'department_id' => $emgDepartmentId ?? $order->service?->service_department_id,
                        'treating_doctor_id' => $actorId,
                        'recorded_by' => $actorId,
                        'treated_at' => Carbon::now(),
                        'triage_id' => $greenTriageId,
                        'is_finalized' => false,
                    ]);

                    if ($greenTriageId) {
                        TriageHistory::create([
                            'treatment_record_id' => $treatmentRecord->id,
                            'service_order_id' => $order->id,
                            'old_triage_id' => null,
                            'new_triage_id' => $greenTriageId,
                            'changed_by' => null,
                            'changed_at' => Carbon::now(),
                        ]);
                    }

                    $order->closed_at = now();
                    $order->status = 'closed';
                    $order->notes = 'Automatically closed (discharged) by system due to being open for more than 12 hours with no treatment recorded.';
                    $order->save();
                }
                $this->info('Processed '.count($orders).' service orders');
            });

        // ServiceOrder::whereNull('closed_at')
        //     // Created 2 months ago or earlier
        //     ->where('created_at', '<=', now()->subMonths(2))
        //     ->where('status', 'open')
        //     ->where('type', 'IND')
        //     ->orderBy('id')
        //     ->chunk(2000, function ($orders) {
        //         foreach ($orders as $order) {
        //             $order->closed_at = now();
        //             $order->status = 'CLOSED';
        //             $order->notes = 'Automatically closed by system due to being open for more than 2 months.';
        //             $order->save();
        //         }
        //         $this->info('Processed '.count($orders).' service orders');
        //     });

        ServiceOrder::whereNull('closed_at')
            // Created 12 hours ago or earlier
            ->where('created_at', '<=', now()->subHours(5))
            ->where('status', 'open')
            ->where('type', 'DNT')
            ->orderBy('id')
            ->chunk(2000, function ($orders) {
                foreach ($orders as $order) {
                    $order->closed_at = now();
                    $order->status = 'CLOSED';
                    $order->notes = 'Automatically closed by system due to being open for more than 5 hours.';
                    $order->save();
                }
                $this->info('Processed '.count($orders).' service orders');
            });

        ServiceOrder::whereNull('closed_at')
            // Created 36 hours ago or earlier
            ->where('created_at', '<=', now()->subHours(1))
            ->where('status', 'open')
            ->where('type', 'ULT')
            ->orderBy('id')
            ->chunk(2000, function ($orders) {
                foreach ($orders as $order) {
                    $order->closed_at = now();
                    $order->status = 'CLOSED';
                    $order->notes = 'Automatically closed by system due to being open for more than 1 hour.';
                    $order->save();
                }
                $this->info('Processed '.count($orders).' service orders');
            });

        $this->info('Finished closing service orders for existing transactions.');
    }
}
