<?php

namespace App\Console\Commands;

use App\Jobs\FulfillOrder;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessNewOrders extends Command
{
    protected $signature = 'orders:process-fulfillment';

    protected $description = 'Dispatch fulfillment jobs for paid orders awaiting fulfillment';

    public function handle(): int
    {
        $orders = Order::where('payment_status', 'paid')
            ->where('fulfillment_status', 'pending')
            ->whereHas('items', function ($query) {
                $query->whereHasMorph('item', ['App\Models\Product'], function ($q) {
                    $q->whereNotNull('fulfillment_provider');
                });
            })
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders awaiting fulfillment.');
            return Command::SUCCESS;
        }

        foreach ($orders as $order) {
            try {
                FulfillOrder::dispatch($order);
                $this->info("Dispatched fulfillment for Order #{$order->order_number}");
            } catch (\Exception $e) {
                Log::error('Failed to dispatch fulfillment job', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to dispatch Order #{$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->info("Dispatched fulfillment for {$orders->count()} order(s).");

        return Command::SUCCESS;
    }
}
