<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\PrintfulService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FulfillOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Order $order
    ) {}

    public function handle(): void
    {
        $order = $this->order->load(['items.item', 'items.variant']);

        // Group order items by fulfillment provider
        $groups = $this->groupItemsByProvider($order);

        foreach ($groups as $provider => $items) {
            match ($provider) {
                'printful' => $this->fulfillWithPrintful($order, $items),
                'manual' => $this->notifyManualFulfillment($order, $items),
                default => $this->notifyManualFulfillment($order, $items),
            };
        }

        $order->update(['fulfillment_status' => 'processing']);
    }

    private function groupItemsByProvider(Order $order): array
    {
        $groups = [];

        foreach ($order->items as $orderItem) {
            $product = $orderItem->item;
            $provider = $product?->fulfillment_provider ?? 'printful';
            $groups[$provider][] = $orderItem;
        }

        return $groups;
    }

    private function fulfillWithPrintful(Order $order, array $items): void
    {
        $printfulService = app(PrintfulService::class);

        $printfulItems = collect($items)->map(function ($orderItem) {
            // Prefer sync_variant_id from the variant model, fall back to order item's printful_variant_id
            $syncVariantId = $orderItem->variant?->printful_sync_variant_id
                ?? $orderItem->printful_variant_id
                ?? $orderItem->item?->fulfillment_sku;

            return [
                'sync_variant_id' => $syncVariantId,
                'quantity' => $orderItem->quantity,
                'retail_price' => (string) $orderItem->unit_price,
            ];
        })->filter(fn ($item) => !empty($item['sync_variant_id']))->values()->toArray();

        if (empty($printfulItems)) {
            Log::warning('No valid Printful SKUs found for order — marking as failed', [
                'order_id' => $order->id,
                'item_count' => count($items),
            ]);
            $order->update(['fulfillment_status' => 'failed']);
            return;
        }

        $result = $printfulService->createOrder($order, $printfulItems);

        $printfulOrderId = $result['id'] ?? null;

        $order->update([
            'fulfillment_provider' => 'printful',
            'fulfillment_order_id' => $printfulOrderId,
        ]);

        Log::info('Printful draft order created', [
            'order_id' => $order->id,
            'printful_order_id' => $printfulOrderId,
        ]);

        // Confirm the draft order so Printful begins fulfillment
        if ($printfulOrderId) {
            $printfulService->confirmOrder($printfulOrderId);
            Log::info('Printful order confirmed for fulfillment', [
                'order_id' => $order->id,
                'printful_order_id' => $printfulOrderId,
            ]);
        }
    }

    private function notifyManualFulfillment(Order $order, array $items): void
    {
        Log::warning('Order requires manual fulfillment', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'item_count' => count($items),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FulfillOrder job failed permanently', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->order->update(['fulfillment_status' => 'failed']);
    }
}
