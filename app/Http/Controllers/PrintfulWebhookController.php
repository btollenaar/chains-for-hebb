<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Mail\OrderStatusUpdateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PrintfulWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature if secret is configured
        $webhookSecret = config('services.printful.webhook_secret');
        if ($webhookSecret) {
            $signature = $request->header('X-Printful-Signature');
            $expectedSignature = base64_encode(hash_hmac('sha256', $request->getContent(), $webhookSecret, true));

            if (!$signature || !hash_equals($expectedSignature, $signature)) {
                Log::warning('Printful webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $event = $request->input('type');
        $data = $request->input('data');

        Log::info('Printful webhook received', ['type' => $event]);

        return match ($event) {
            'package_shipped' => $this->handlePackageShipped($data),
            'order_failed' => $this->handleOrderFailed($data),
            'order_canceled' => $this->handleOrderCanceled($data),
            'product_updated' => $this->handleProductUpdated($data),
            'stock_updated' => $this->handleStockUpdated($data),
            default => response()->json(['status' => 'unhandled']),
        };
    }

    private function handlePackageShipped(array $data): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($data['order']['external_id'] ?? null);
        if (!$order) {
            return response()->json(['status' => 'order_not_found'], 404);
        }

        $order->update([
            'fulfillment_status' => 'shipped',
            'tracking_number' => $data['shipment']['tracking_number'] ?? null,
            'tracking_carrier' => $this->mapCarrier($data['shipment']['carrier'] ?? ''),
            'shipped_at' => now(),
        ]);

        try {
            Mail::to($order->customer->email)->send(new OrderStatusUpdateMail($order, 'shipped'));
        } catch (\Exception $e) {
            Log::error('Printful shipped notification email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['status' => 'processed']);
    }

    private function handleOrderFailed(array $data): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($data['order']['external_id'] ?? null);
        if (!$order) {
            return response()->json(['status' => 'order_not_found'], 404);
        }

        $order->update(['fulfillment_status' => 'failed']);

        Log::error('Printful order fulfillment failed', [
            'order_id' => $order->id,
            'reason' => $data['reason'] ?? 'unknown',
        ]);

        return response()->json(['status' => 'processed']);
    }

    private function handleOrderCanceled(array $data): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($data['order']['external_id'] ?? null);
        if (!$order) {
            return response()->json(['status' => 'order_not_found'], 404);
        }

        $order->update(['fulfillment_status' => 'cancelled']);

        Log::warning('Printful order cancelled', [
            'order_id' => $order->id,
        ]);

        return response()->json(['status' => 'processed']);
    }

    private function handleProductUpdated(array $data): \Illuminate\Http\JsonResponse
    {
        $syncProductId = $data['sync_product']['id'] ?? null;
        if (!$syncProductId) {
            return response()->json(['status' => 'no_sync_product_id'], 400);
        }

        $product = Product::where('printful_sync_product_id', $syncProductId)->first();
        if (!$product) {
            return response()->json(['status' => 'product_not_found']);
        }

        // Update variant info from Printful sync data
        foreach ($data['sync_variants'] ?? [] as $syncVariant) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('printful_sync_variant_id', $syncVariant['id'])
                ->first();

            if ($variant && isset($syncVariant['retail_price'])) {
                $variant->update([
                    'printful_cost' => (float) ($syncVariant['product']['price'] ?? $variant->printful_cost),
                ]);
            }
        }

        Log::info('Printful product updated via webhook', [
            'product_id' => $product->id,
            'sync_product_id' => $syncProductId,
        ]);

        return response()->json(['status' => 'processed']);
    }

    private function handleStockUpdated(array $data): \Illuminate\Http\JsonResponse
    {
        $syncProductId = $data['sync_product']['id'] ?? null;

        $product = $syncProductId
            ? Product::where('printful_sync_product_id', $syncProductId)->first()
            : null;

        if (!$product) {
            return response()->json(['status' => 'product_not_found']);
        }

        foreach ($data['sync_variants'] ?? [] as $syncVariant) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('printful_sync_variant_id', $syncVariant['id'])
                ->first();

            if ($variant) {
                $inStock = ($syncVariant['availability_status'] ?? 'active') !== 'discontinued';
                $variant->update([
                    'stock_status' => $inStock ? 'in_stock' : 'out_of_stock',
                ]);
            }
        }

        Log::info('Printful stock updated via webhook', [
            'product_id' => $product->id,
            'sync_product_id' => $syncProductId,
        ]);

        return response()->json(['status' => 'processed']);
    }

    private function mapCarrier(string $printfulCarrier): string
    {
        return match (strtolower($printfulCarrier)) {
            'usps' => 'usps',
            'ups' => 'ups',
            'fedex' => 'fedex',
            'dhl', 'dhl_express' => 'dhl',
            default => 'other',
        };
    }
}
