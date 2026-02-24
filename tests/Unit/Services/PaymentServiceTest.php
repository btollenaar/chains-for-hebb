<?php

namespace Tests\Unit\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_stripe_checkout_session_with_correct_parameters()
    {
        $customer = Customer::factory()->create(['email' => 'test@example.com']);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'total_amount' => 100.00,
            'tax_amount' => 6.50,
        ]);

        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 93.50,
        ]);

        OrderItem::factory()->forProduct($product)->create([
            'order_id' => $order->id,
            'quantity' => 1,
        ]);

        $order->load('items');

        // Mock Stripe Session creation
        $mockSession = Mockery::mock('overload:' . StripeSession::class);
        $mockSession->shouldReceive('create')
            ->once()
            ->withArgs(function ($args) use ($customer, $order) {
                return $args['customer_email'] === $customer->email
                    && $args['client_reference_id'] === $order->id
                    && $args['mode'] === 'payment'
                    && $args['payment_method_types'] === ['card']
                    && isset($args['line_items'])
                    && isset($args['success_url'])
                    && isset($args['cancel_url'])
                    && $args['metadata']['order_id'] === $order->id
                    && $args['metadata']['customer_id'] === $customer->id;
            })
            ->andReturn((object)[
                'id' => 'cs_test_123',
                'url' => 'https://checkout.stripe.com/pay/cs_test_123',
            ]);

        $redirectUrl = $this->paymentService->processStripePayment($order, $customer);

        $this->assertEquals('https://checkout.stripe.com/pay/cs_test_123', $redirectUrl);
        $order->refresh();
        $this->assertEquals('cs_test_123', $order->stripe_session_id);
    }

    /** @test */
    public function it_creates_line_items_with_correct_pricing()
    {
        $order = Order::factory()->create([
            'tax_amount' => 6.50,
        ]);

        $product1 = Product::factory()->create([
            'name' => 'Product 1',
            'price' => 50.00,
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Product 2',
            'price' => 75.00,
        ]);

        OrderItem::factory()->forProduct($product1)->create([
            'order_id' => $order->id,
            'quantity' => 2,
        ]);
        OrderItem::factory()->forProduct($product2)->create([
            'order_id' => $order->id,
            'quantity' => 1,
        ]);

        $order->load('items');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('createStripeLineItems');
        $method->setAccessible(true);

        $lineItems = $method->invoke($this->paymentService, $order);

        // Should have 3 line items (2 products + tax)
        $this->assertCount(3, $lineItems);

        // Check first product
        $this->assertEquals('Product 1', $lineItems[0]['price_data']['product_data']['name']);
        $this->assertEquals(5000, $lineItems[0]['price_data']['unit_amount']); // $50 in cents
        $this->assertEquals(2, $lineItems[0]['quantity']);

        // Check second product
        $this->assertEquals('Product 2', $lineItems[1]['price_data']['product_data']['name']);
        $this->assertEquals(7500, $lineItems[1]['price_data']['unit_amount']); // $75 in cents
        $this->assertEquals(1, $lineItems[1]['quantity']);

        // Check tax line item
        $this->assertEquals('Tax', $lineItems[2]['price_data']['product_data']['name']);
        $this->assertEquals(650, $lineItems[2]['price_data']['unit_amount']); // $6.50 in cents
        $this->assertEquals(1, $lineItems[2]['quantity']);
    }

    /** @test */
    public function it_does_not_add_tax_line_item_when_tax_is_zero()
    {
        $order = Order::factory()->create([
            'tax_amount' => 0,
        ]);

        $product = Product::factory()->create([
            'name' => 'Product',
            'price' => 50.00,
        ]);

        OrderItem::factory()->forProduct($product)->create([
            'order_id' => $order->id,
            'quantity' => 1,
        ]);

        $order->load('items');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('createStripeLineItems');
        $method->setAccessible(true);

        $lineItems = $method->invoke($this->paymentService, $order);

        // Should only have 1 line item (product, no tax)
        $this->assertCount(1, $lineItems);
        $this->assertEquals('Product', $lineItems[0]['price_data']['product_data']['name']);
    }

    /** @test */
    public function it_verifies_stripe_payment_and_updates_order()
    {
        $order = Order::factory()->create([
            'payment_status' => 'pending',
            'stripe_session_id' => 'cs_test_123',
        ]);

        // Mock Stripe Session retrieval
        $mockSession = Mockery::mock('overload:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_123')
            ->andReturn((object)[
                'id' => 'cs_test_123',
                'client_reference_id' => $order->id,
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_456',
            ]);

        $result = $this->paymentService->verifyStripePayment('cs_test_123', $order);

        $this->assertTrue($result);
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('pi_test_456', $order->stripe_payment_intent_id);
    }

    /** @test */
    public function it_returns_false_when_payment_not_paid()
    {
        $order = Order::factory()->create([
            'payment_status' => 'pending',
        ]);

        // Mock Stripe Session with unpaid status
        $mockSession = Mockery::mock('overload:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_123')
            ->andReturn((object)[
                'id' => 'cs_test_123',
                'client_reference_id' => $order->id,
                'payment_status' => 'unpaid', // Not paid
                'payment_intent' => null,
            ]);

        $result = $this->paymentService->verifyStripePayment('cs_test_123', $order);

        $this->assertFalse($result);
        $order->refresh();
        $this->assertEquals('pending', $order->payment_status); // Should not change
    }

    /** @test */
    public function it_returns_false_when_session_does_not_match_order()
    {
        $order = Order::factory()->create(['id' => 1]);

        // Mock Stripe Session with wrong order ID
        $mockSession = Mockery::mock('overload:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_123')
            ->andReturn((object)[
                'id' => 'cs_test_123',
                'client_reference_id' => 999, // Wrong order ID
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_456',
            ]);

        $result = $this->paymentService->verifyStripePayment('cs_test_123', $order);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_handles_stripe_api_exceptions_gracefully()
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Stripe session verification failed'
                    && isset($context['session_id'])
                    && isset($context['order_id'])
                    && isset($context['error']);
            });

        $order = Order::factory()->create();

        // Mock Stripe Session to throw exception
        $mockSession = Mockery::mock('overload:' . StripeSession::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_invalid')
            ->andThrow(new \Exception('Invalid session ID'));

        $result = $this->paymentService->verifyStripePayment('cs_test_invalid', $order);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_throws_exception_for_paypal_payment()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PayPal payment is not currently available.');

        $customer = Customer::factory()->create();
        $order = Order::factory()->create();

        $this->paymentService->processPayPalPayment($order, $customer);
    }

    /** @test */
    public function it_uses_correct_currency_in_line_items()
    {
        config(['business.payments.currency' => 'USD']);

        $order = Order::factory()->create(['tax_amount' => 0]);
        $product = Product::factory()->create(['price' => 50.00]);

        OrderItem::factory()->forProduct($product)->create([
            'order_id' => $order->id,
            'quantity' => 1,
        ]);

        $order->load('items');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('createStripeLineItems');
        $method->setAccessible(true);

        $lineItems = $method->invoke($this->paymentService, $order);

        $this->assertEquals('usd', $lineItems[0]['price_data']['currency']);
    }

    /** @test */
    public function it_includes_product_description_in_line_items_when_available()
    {
        $order = Order::factory()->create(['tax_amount' => 0]);
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 50.00,
        ]);

        OrderItem::factory()->forProduct($product)->create([
            'order_id' => $order->id,
            'quantity' => 1,
        ]);

        $order->load('items');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('createStripeLineItems');
        $method->setAccessible(true);

        $lineItems = $method->invoke($this->paymentService, $order);

        $this->assertArrayHasKey('description', $lineItems[0]['price_data']['product_data']);
    }
}
