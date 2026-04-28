<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use Illuminate\Console\Command;

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

        ServiceOrder::whereNull('closed_at')
            // Created 6 hours ago or earlier
            ->where('created_at', '<=', now()->subHour(6))
            ->where('status', 'open')
            ->where('type', 'EMG')
            ->orderBy('id')
            ->chunk(2000, function ($orders) {
                foreach ($orders as $order) {
                    $order->closed_at = now();
                    $order->status = 'CLOSED';
                    $order->notes = 'Automatically closed by system due to being open for more than 6 hours.';
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
