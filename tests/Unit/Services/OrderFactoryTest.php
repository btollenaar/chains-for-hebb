<?php

namespace Tests\Unit\Services;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class OrderFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected OrderFactory $orderFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderFactory = new OrderFactory();
    }

    /** @test */
    public function it_creates_order_from_cart_with_correct_totals()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);

        $cartItems = collect([
            (object) [
                'item' => $product,
                'item_type' => Product::class,
                'item_id' => $product->id,
                'quantity' => 2,
                'attributes' => null,
            ],
        ]);

        $shippingAddress = [
            'street' => '123 Main St',
            'city' => 'Tampa',
            'state' => 'FL',
            'zip' => '33602',
            'country' => 'US',
        ];

        $billingAddress = $shippingAddress;

        $order = $this->orderFactory->createOrderFromCart(
            $customer,
            $cartItems,
            $shippingAddress,
            $billingAddress,
            'stripe',
            'Test notes'
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals(200.00, $order->subtotal); // 2 * $100
        $this->assertEquals('stripe', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('pending', $order->fulfillment_status);
        $this->assertEquals($shippingAddress, $order->shipping_address);
        $this->assertEquals($billingAddress, $order->billing_address);
        $this->assertEquals('Test notes', $order->notes);
    }

    /** @test */
    public function it_creates_order_items_with_correct_snapshots()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 50.00,
            'stock_quantity' => 10,
        ]);

        // Use actual Cart model with eager loaded relationship
        $cartItem = Cart::factory()->forProduct($product)->create([
            'customer_id' => $customer->id,
            'quantity' => 3,
            'attributes' => ['color' => 'blue'],
        ]);

        $cartItems = collect([
            Cart::with('item')->find($cartItem->id)
        ]);

        $addresses = [
            'street' => '123 Main St',
            'city' => 'Tampa',
            'state' => 'FL',
            'zip' => '33602',
            'country' => 'US',
        ];

        $order = $this->orderFactory->createOrderFromCart(
            $customer,
            $cartItems,
            $addresses,
            $addresses,
            'stripe'
        );

        $this->assertCount(1, $order->items);

        $orderItem = $order->items->first();
        $this->assertEquals(Product::class, $orderItem->item_type);
        $this->assertEquals($product->id, $orderItem->item_id);
        $this->assertEquals(3, $orderItem->quantity);
        $this->assertEquals(['color' => 'blue'], $orderItem->attributes);
        // Snapshot data is stored directly in fields, not an array
        $this->assertEquals('Test Product', $orderItem->name);
        $this->assertEquals(50.00, $orderItem->unit_price);
    }

    /** @test */
    public function it_decrements_product_stock_when_creating_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);

        $initialStock = $product->stock_quantity;

        $cartItems = collect([
            (object) [
                'item' => $product,
                'item_type' => Product::class,
                'item_id' => $product->id,
                'quantity' => 3,
                'attributes' => null,
            ],
        ]);

        $addresses = [
            'street' => '123 Main St',
            'city' => 'Tampa',
            'state' => 'FL',
            'zip' => '33602',
            'country' => 'US',
        ];

        $this->orderFactory->createOrderFromCart(
            $customer,
            $cartItems,
            $addresses,
            $addresses,
            'stripe'
        );

        $product->refresh();
        $this->assertEquals($initialStock - 3, $product->stock_quantity);
    }

    /** @test */
    public function it_calculates_order_totals_with_tax()
    {
        config(['business.payments.tax_rate' => 0.065]); // 6.5% tax

        $product1 = Product::factory()->create(['price' => 100.00]);
        $product2 = Product::factory()->create(['price' => 50.00]);

        $cartItems = collect([
            (object) ['item' => $product1, 'quantity' => 2],
            (object) ['item' => $product2, 'quantity' => 1],
        ]);

        $totals = $this->orderFactory->calculateOrderTotals($cartItems);

        $this->assertEquals(250.00, $totals['subtotal']); // (100*2) + (50*1)
        $this->assertEquals(16.25, $totals['tax']); // 250 * 0.065
        $this->assertEquals(266.25, $totals['total']); // 250 + 16.25
    }

    /** @test */
    public function it_uses_sale_price_when_calculating_totals()
    {
        config(['business.payments.tax_rate' => 0.065]);

        $product = Product::factory()->create([
            'price' => 100.00,
            'sale_price' => 80.00, // Sale price should be used
        ]);

        $cartItems = collect([
            (object) ['item' => $product, 'quantity' => 1],
        ]);

        $totals = $this->orderFactory->calculateOrderTotals($cartItems);

        $this->assertEquals(80.00, $totals['subtotal']); // Uses sale_price
        $this->assertEquals(5.20, $totals['tax']); // 80 * 0.065
        $this->assertEquals(85.20, $totals['total']);
    }

    /** @test */
    public function it_builds_address_arrays_with_same_as_shipping()
    {
        $validated = [
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Tampa',
            'shipping_state' => 'FL',
            'shipping_zip' => '33602',
            'shipping_country' => 'US',
        ];

        $addresses = $this->orderFactory->buildAddressArrays($validated, true);

        $this->assertEquals($addresses['shipping'], $addresses['billing']);
        $this->assertEquals('123 Main St', $addresses['shipping']['street']);
        $this->assertEquals('Tampa', $addresses['shipping']['city']);
        $this->assertEquals('FL', $addresses['shipping']['state']);
        $this->assertEquals('33602', $addresses['shipping']['zip']);
        $this->assertEquals('US', $addresses['shipping']['country']);
    }

    /** @test */
    public function it_builds_separate_billing_address_when_not_same_as_shipping()
    {
        $validated = [
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Tampa',
            'shipping_state' => 'FL',
            'shipping_zip' => '33602',
            'shipping_country' => 'US',
            'billing_street' => '456 Oak Ave',
            'billing_city' => 'Miami',
            'billing_state' => 'FL',
            'billing_zip' => '33101',
            'billing_country' => 'US',
        ];

        $addresses = $this->orderFactory->buildAddressArrays($validated, false);

        $this->assertNotEquals($addresses['shipping'], $addresses['billing']);
        $this->assertEquals('123 Main St', $addresses['shipping']['street']);
        $this->assertEquals('Tampa', $addresses['shipping']['city']);
        $this->assertEquals('456 Oak Ave', $addresses['billing']['street']);
        $this->assertEquals('Miami', $addresses['billing']['city']);
    }

    /** @test */
    public function it_calculates_totals_for_empty_cart()
    {
        $cartItems = collect([]);

        $totals = $this->orderFactory->calculateOrderTotals($cartItems);

        $this->assertEquals(0, $totals['subtotal']);
        $this->assertEquals(0, $totals['tax']);
        $this->assertEquals(0, $totals['total']);
    }
}
