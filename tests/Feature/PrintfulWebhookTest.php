<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintfulWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_shipped_webhook_updates_order()
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'fulfillment_provider' => 'printful',
            'fulfillment_status' => 'processing',
        ]);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'package_shipped',
            'data' => [
                'order' => ['external_id' => $order->id],
                'shipment' => [
                    'tracking_number' => '9400111899223456789012',
                    'carrier' => 'USPS',
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'processed']);

        $order->refresh();
        $this->assertEquals('shipped', $order->fulfillment_status);
        $this->assertEquals('9400111899223456789012', $order->tracking_number);
        $this->assertEquals('usps', $order->tracking_carrier);
        $this->assertNotNull($order->shipped_at);
    }

    public function test_order_failed_webhook_updates_status()
    {
        $order = Order::factory()->create([
            'fulfillment_provider' => 'printful',
            'fulfillment_status' => 'processing',
        ]);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'order_failed',
            'data' => [
                'order' => ['external_id' => $order->id],
                'reason' => 'Design file too low resolution',
            ],
        ]);

        $response->assertOk();
        $order->refresh();
        $this->assertEquals('failed', $order->fulfillment_status);
    }

    public function test_order_canceled_webhook_updates_status()
    {
        $order = Order::factory()->create([
            'fulfillment_provider' => 'printful',
            'fulfillment_status' => 'processing',
        ]);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'order_canceled',
            'data' => [
                'order' => ['external_id' => $order->id],
            ],
        ]);

        $response->assertOk();
        $order->refresh();
        $this->assertEquals('cancelled', $order->fulfillment_status);
    }

    public function test_webhook_returns_404_for_unknown_order()
    {
        $response = $this->postJson('/printful/webhook', [
            'type' => 'package_shipped',
            'data' => [
                'order' => ['external_id' => 99999],
                'shipment' => ['tracking_number' => '123', 'carrier' => 'UPS'],
            ],
        ]);

        $response->assertStatus(404);
        $response->assertJson(['status' => 'order_not_found']);
    }

    public function test_unhandled_event_type_returns_unhandled()
    {
        $response = $this->postJson('/printful/webhook', [
            'type' => 'some_unknown_event',
            'data' => [],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'unhandled']);
    }

    public function test_invalid_signature_rejected_when_secret_configured()
    {
        config(['services.printful.webhook_secret' => 'test-secret-key']);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'package_shipped',
            'data' => [],
        ], ['X-Printful-Signature' => 'invalid-signature']);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid signature']);
    }

    public function test_stock_updated_webhook_updates_variant_status()
    {
        $product = Product::factory()->create([
            'fulfillment_type' => 'printful',
            'printful_sync_product_id' => 12345,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'printful_sync_variant_id' => 67890,
            'stock_status' => 'in_stock',
        ]);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'stock_updated',
            'data' => [
                'sync_product' => ['id' => 12345],
                'sync_variants' => [
                    [
                        'id' => 67890,
                        'availability_status' => 'discontinued',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
        $variant->refresh();
        $this->assertEquals('out_of_stock', $variant->stock_status);
    }

    public function test_product_updated_webhook_syncs_cost()
    {
        $product = Product::factory()->create([
            'fulfillment_type' => 'printful',
            'printful_sync_product_id' => 12345,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'printful_sync_variant_id' => 67890,
            'printful_cost' => 10.00,
        ]);

        $response = $this->postJson('/printful/webhook', [
            'type' => 'product_updated',
            'data' => [
                'sync_product' => ['id' => 12345],
                'sync_variants' => [
                    [
                        'id' => 67890,
                        'retail_price' => '29.99',
                        'product' => ['price' => '12.50'],
                    ],
                ],
            ],
        ]);

        $response->assertOk();
        $variant->refresh();
        $this->assertEquals(12.50, (float) $variant->printful_cost);
    }

    public function test_carrier_mapping()
    {
        $customer = Customer::factory()->create();

        $carriers = [
            'USPS' => 'usps',
            'UPS' => 'ups',
            'FedEx' => 'fedex',
            'DHL' => 'dhl',
            'DHL_EXPRESS' => 'dhl',
            'SomeOther' => 'other',
        ];

        foreach ($carriers as $printfulCarrier => $expected) {
            $order = Order::factory()->create([
                'customer_id' => $customer->id,
                'fulfillment_provider' => 'printful',
                'fulfillment_status' => 'processing',
            ]);

            $this->postJson('/printful/webhook', [
                'type' => 'package_shipped',
                'data' => [
                    'order' => ['external_id' => $order->id],
                    'shipment' => [
                        'tracking_number' => "TRACK-{$printfulCarrier}",
                        'carrier' => $printfulCarrier,
                    ],
                ],
            ]);

            $order->refresh();
            $this->assertEquals($expected, $order->tracking_carrier, "Carrier mapping failed for: {$printfulCarrier}");
        }
    }
}
