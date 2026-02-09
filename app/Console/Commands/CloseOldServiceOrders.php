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

        $serviceOrders = ServiceOrder::whereNull('closed_at')
            // Created 24 hours ago or earlier
            ->where('created_at', '<=', now()->subDay())
            ->orderBy('id')
            ->chunk(2000, function ($orders) {
                foreach ($orders as $order) {
                    $order->closed_at = now();
                    $order->status = 'CLOSED';
                    $order->save();
                }
                $this->info('Processed ' . count($orders) . ' service orders');
            });

        $this->info('Finished closing service orders for existing transactions.');
    }
}
