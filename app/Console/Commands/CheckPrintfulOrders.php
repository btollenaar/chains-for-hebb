<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PrintfulService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPrintfulOrders extends Command
{
    protected $signature = 'printful:check-orders';
    protected $description = 'Poll Printful for status updates on unfulfilled orders (webhook backup)';

    public function handle(PrintfulService $printful): int
    {
        $this->info('Checking Printful order statuses...');

        $orders = Order::where('fulfillment_provider', 'printful')
            ->whereNotNull('fulfillment_order_id')
            ->whereNotIn('fulfillment_status', ['delivered', 'cancelled', 'returned'])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No pending Printful orders to check.');
            return self::SUCCESS;
        }

        $updated = 0;
        $failed = 0;

        foreach ($orders as $order) {
            try {
                $printfulOrder = $printful->getOrder((int) $order->fulfillment_order_id);

                $status = $printfulOrder['status'] ?? null;
                $shipments = $printfulOrder['shipments'] ?? [];

                $newFulfillmentStatus = $this->mapPrintfulStatus($status);

                if ($newFulfillmentStatus && $order->fulfillment_status !== $newFulfillmentStatus) {
                    $updateData = ['fulfillment_status' => $newFulfillmentStatus];

                    // Extract tracking info from first shipment
                    if (!empty($shipments[0])) {
                        $shipment = $shipments[0];
                        if (!empty($shipment['tracking_number'])) {
                            $updateData['tracking_number'] = $shipment['tracking_number'];
                        }
                        if (!empty($shipment['carrier'])) {
                            $updateData['tracking_carrier'] = $shipment['carrier'];
                        }
                        if (!empty($shipment['ship_date'])) {
                            $updateData['shipped_at'] = $shipment['ship_date'];
                        }
                    }

                    $order->update($updateData);
                    $updated++;

                    $this->line("Order #{$order->id}: {$order->fulfillment_status} → {$newFulfillmentStatus}");
                }
            } catch (\Exception $e) {
                Log::warning('Printful order check failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info("Order check complete. Updated: {$updated}. Failed: {$failed}.");
        return self::SUCCESS;
    }

    private function mapPrintfulStatus(?string $printfulStatus): ?string
    {
        return match ($printfulStatus) {
            'pending', 'draft' => 'processing',
            'inprocess' => 'processing',
            'fulfilled', 'shipped' => 'shipped',
            'canceled', 'cancelled' => 'cancelled',
            'failed' => 'failed',
            default => null,
        };
    }
}
