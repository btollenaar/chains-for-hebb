<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper method to create a cart with items for testing
     */
    protected function createCartWithProducts(Customer $customer, int $productCount = 1): void
    {
        $products = Product::factory()->count($productCount)->create([
            'price' => 50.00,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);

        foreach ($products as $product) {
            Cart::create([
                'customer_id' => $customer->id,
                'session_id' => null,
                'item_type' => Product::class,
                'item_id' => $product->id,
                'quantity' => 2,
            ]);
        }
    }

    /**
     * Test 1: Authenticated customer checkout with cash payment
     * Happy path - most common successful checkout scenario
     */
    public function test_authenticated_customer_checkout_with_cash(): void
    {
        // Arrange
        Mail::fake();

        // Enable cash payment method in config
        config(['business.payments.enabled_methods.cash' => true]);
        config(['business.payments.tax_rate' => 0.07]);

        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer);

        $checkoutData = [
            'name' => 'John Doe',
            'email' => $customer->email,
            'phone' => '555-1234',
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Tampa',
            'shipping_state' => 'FL',
            'shipping_zip' => '33601',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
            'notes' => 'Please deliver ASAP',
            'newsletter_opt_in' => false,
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Debug: Check for errors
        if ($response->status() !== 302) {
            dump($response->getContent());
            dump($response->getStatusCode());
        }
        if (session()->has('error')) {
            dump('Session error: ' . session('error'));
        }

        // Assert: Order created
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        // Assert: Order confirmation email sent
        Mail::assertSent(OrderConfirmationMail::class);

        // Assert: Redirected to success page
        $response->assertRedirect();

        // Assert: Cart cleared
        $this->assertEquals(0, Cart::where('customer_id', $customer->id)->count());
    }

    /**
     * Test 2: Checkout validates stock availability
     * Critical - prevents overselling
     */
    public function test_checkout_validates_stock_availability(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 5, // Only 5 in stock
            'status' => 'active',
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 10, // Trying to buy 10 (more than available)
        ]);

        $checkoutData = [
            'name' => 'Test User',
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'stripe',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Redirected back with error
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');

        // Assert: No order created
        $this->assertEquals(0, Order::count());
    }

    /**
     * Test 3: Checkout decrements stock after payment
     * Revenue critical - inventory management
     */
    public function test_checkout_decrements_stock_after_order_creation(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 3,
        ]);

        $checkoutData = [
            'name' => 'Test User',
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'stripe',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Stock decremented
        $product->refresh();
        $this->assertEquals(97, $product->stock_quantity); // 100 - 3 = 97
    }

    /**
     * Test 4: Failed checkout transaction rolls back stock
     * Critical - prevents inventory corruption on errors
     */
    public function test_failed_checkout_does_not_decrement_stock(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 3,
        ]);

        // Invalid checkout data (missing required fields) to trigger failure
        $checkoutData = [
            'email' => $customer->email,
            // Missing required shipping fields
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Validation error
        $response->assertSessionHasErrors();

        // Assert: Stock unchanged
        $product->refresh();
        $this->assertEquals(100, $product->stock_quantity);
    }

    /**
     * Test 5: Order totals calculated correctly with tax
     * Math accuracy critical for revenue
     */
    public function test_order_totals_calculated_correctly_with_tax(): void
    {
        // Arrange
        Mail::fake();
        config(['business.payments.tax_rate' => 0.07]); // 7% tax

        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 50,
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 2, // 2 × $100 = $200
        ]);

        $checkoutData = [
            'name' => 'Test User',
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'free',
        ];

        // Act
        $this->post(route('checkout.process'), $checkoutData);

        // Assert: Order totals correct
        $order = Order::first();
        $this->assertEquals(200.00, $order->subtotal); // 2 × $100
        $this->assertEquals(14.00, $order->tax_amount); // $200 × 0.07
        $this->assertEquals(214.00, $order->total_amount); // $200 + $14
    }

    /**
     * Test 6: New customer can complete checkout
     * Tests that first-time customers can successfully check out
     */
    public function test_new_customer_can_complete_checkout(): void
    {
        // Arrange - Create new customer (simulating first-time buyer)
        Mail::fake();
        $customer = Customer::factory()->create([
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'password' => null, // No password set (like a guest checkout)
        ]);

        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 100,
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => '555-9999',
            'shipping_street' => '789 New St',
            'shipping_city' => 'Orlando',
            'shipping_state' => 'FL',
            'shipping_zip' => '32801',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Order created for new customer
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
        ]);
    }

    /**
     * Test 7: Cash payment creates pending order
     * Tests alternative payment method
     */
    public function test_cash_payment_creates_pending_order(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer);

        $checkoutData = [
            'name' => 'Cash Customer',
            'email' => $customer->email,
            'shipping_street' => '789 Cash Rd',
            'shipping_city' => 'Orlando',
            'shipping_state' => 'FL',
            'shipping_zip' => '32801',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Order created with pending status
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        // Assert: Order confirmation email sent
        Mail::assertSent(OrderConfirmationMail::class);

        // Assert: Redirected to success page
        $response->assertRedirect();
    }

    /**
     * Test 8: Cart cleared after successful checkout
     * Ensures cart cleanup
     */
    public function test_cart_cleared_after_successful_checkout(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer, 3); // 3 products in cart

        // Verify cart has items
        $this->assertEquals(3, Cart::where('customer_id', $customer->id)->count());

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $this->post(route('checkout.process'), $checkoutData);

        // Assert: Cart empty
        $this->assertEquals(0, Cart::where('customer_id', $customer->id)->count());
    }

    /**
     * Test 9: Order snapshot captures item details
     * Critical - price freeze at purchase time
     */
    public function test_order_snapshot_captures_item_details(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'name' => 'Original Product Name',
            'price' => 99.99,
            'stock_quantity' => 50,
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $this->post(route('checkout.process'), $checkoutData);

        // Change product price after order
        $product->update(['price' => 199.99, 'name' => 'New Product Name']);

        // Assert: Order item snapshot preserves original data
        $order = Order::first();
        $orderItem = $order->items->first();

        $this->assertEquals('Original Product Name', $orderItem->name);
        $this->assertEquals(99.99, $orderItem->unit_price);

        // Product changed but order snapshot unchanged
        $product->refresh();
        $this->assertEquals(199.99, $product->price);
        $this->assertEquals('New Product Name', $product->name);
    }

    /**
     * Test 10: Checkout requires complete billing address
     * When different from shipping
     */
    public function test_checkout_requires_complete_billing_address(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Shipping St',
            'shipping_city' => 'Tampa',
            'shipping_state' => 'FL',
            'shipping_zip' => '33601',
            'shipping_country' => 'US',
            'same_as_shipping' => false, // Different billing address
            // Missing billing fields
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Validation error
        $response->assertSessionHasErrors(['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country']);
    }

    /**
     * Test 12: Shipping address defaults to billing when same_as_shipping
     * Tests address handling logic
     */
    public function test_shipping_address_defaults_to_billing(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Main St',
            'shipping_city' => 'Tampa',
            'shipping_state' => 'FL',
            'shipping_zip' => '33601',
            'shipping_country' => 'US',
            'same_as_shipping' => true, // Billing = Shipping
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $this->post(route('checkout.process'), $checkoutData);

        // Assert: Both addresses identical
        $order = Order::first();
        $this->assertEquals($order->shipping_address, $order->billing_address);
        $this->assertEquals('123 Main St', $order->billing_address['street']);
    }

    /**
     * Test 13: Empty cart prevents checkout
     * Security - prevents empty order creation
     */
    public function test_empty_cart_prevents_checkout(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        // No items in cart

        // Act
        $response = $this->get(route('checkout.index'));

        // Assert: Redirected to cart with error
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test 14: Out of stock product prevents checkout
     * Critical inventory protection
     */
    public function test_out_of_stock_product_prevents_checkout(): void
    {
        // Arrange
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock_quantity' => 0, // Out of stock
            'status' => 'active',
        ]);

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 1,
        ]);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
        ];

        // Act
        $response = $this->post(route('checkout.process'), $checkoutData);

        // Assert: Redirected with error
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('out of stock', session('error'));

        // Assert: No order created
        $this->assertEquals(0, Order::count());
    }

    /**
     * Test 15: Newsletter opt-in creates subscription
     * Tests marketing feature integration
     */
    public function test_newsletter_opt_in_creates_subscription(): void
    {
        // Arrange
        Mail::fake();
        $customer = Customer::factory()->create();
        $this->actingAs($customer);

        $this->createCartWithProducts($customer);

        $checkoutData = [
            'name' => $customer->name,
            'email' => $customer->email,
            'shipping_street' => '123 Test St',
            'shipping_city' => 'Test City',
            'shipping_state' => 'FL',
            'shipping_zip' => '12345',
            'shipping_country' => 'US',
            'same_as_shipping' => true,
            'payment_method' => 'cash',
            'shipping_method' => 'standard',
            'newsletter_opt_in' => true, // Opt in to newsletter
        ];

        // Act
        $this->post(route('checkout.process'), $checkoutData);

        // Assert: Newsletter subscription created
        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => $customer->email,
            'customer_id' => $customer->id,
            'source' => 'checkout',
            'is_active' => true,
        ]);
    }
}
