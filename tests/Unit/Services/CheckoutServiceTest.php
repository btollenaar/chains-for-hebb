<?php

namespace Tests\Unit\Services;

use App\Mail\ClaimAccountMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\NewsletterSubscription;
use App\Models\Order;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CheckoutService $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = new CheckoutService();
    }

    /** @test */
    public function it_validates_stock_availability_successfully()
    {
        $product = Product::factory()->create([
            'stock_quantity' => 10,
        ]);

        $cartItems = collect([
            (object) [
                'item' => $product,
                'quantity' => 5,
            ],
        ]);

        $result = $this->checkoutService->validateStockAvailability($cartItems);

        $this->assertNull($result); // No error
    }

    /** @test */
    public function it_returns_error_when_product_out_of_stock()
    {
        $product = Product::factory()->create([
            'name' => 'Out of Stock Product',
            'stock_quantity' => 0,
        ]);

        $cartItems = collect([
            (object) [
                'item' => $product,
                'quantity' => 1,
            ],
        ]);

        $result = $this->checkoutService->validateStockAvailability($cartItems);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Out of Stock Product', $result['error']);
        $this->assertStringContainsString('out of stock', $result['error']);
    }

    /** @test */
    public function it_returns_error_when_quantity_exceeds_stock()
    {
        $product = Product::factory()->create([
            'name' => 'Limited Stock Product',
            'stock_quantity' => 3,
        ]);

        $cartItems = collect([
            (object) [
                'item' => $product,
                'quantity' => 5,
            ],
        ]);

        $result = $this->checkoutService->validateStockAvailability($cartItems);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Limited Stock Product', $result['error']);
        $this->assertStringContainsString('3 units', $result['error']);
    }

    /** @test */
    public function it_processes_newsletter_opt_in()
    {
        $customer = Customer::factory()->create();

        $this->checkoutService->processNewsletterOptIn(
            true,
            'test@example.com',
            'Test Customer',
            $customer->id
        );

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'test@example.com',
            'name' => 'Test Customer',
            'customer_id' => $customer->id,
            'source' => 'checkout',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_does_not_create_subscription_when_opt_in_is_false()
    {
        $customer = Customer::factory()->create();

        $this->checkoutService->processNewsletterOptIn(
            false,
            'test@example.com',
            'Test Customer',
            $customer->id
        );

        $this->assertDatabaseMissing('newsletter_subscriptions', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_updates_existing_newsletter_subscription()
    {
        $customer = Customer::factory()->create();

        // Create existing subscription (inactive)
        NewsletterSubscription::create([
            'email' => 'test@example.com',
            'name' => 'Old Name',
            'source' => 'manual',
            'is_active' => false,
            'subscribed_at' => now()->subDays(30),
        ]);

        $this->checkoutService->processNewsletterOptIn(
            true,
            'test@example.com',
            'New Name',
            $customer->id
        );

        $subscription = NewsletterSubscription::where('email', 'test@example.com')->first();
        $this->assertEquals('New Name', $subscription->name);
        $this->assertEquals($customer->id, $subscription->customer_id);
        $this->assertEquals('checkout', $subscription->source);
        $this->assertTrue($subscription->is_active);
    }

    /** @test */
    public function it_clears_authenticated_customer_cart()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        Cart::create([
            'customer_id' => $customer->id,
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->checkoutService->clearCustomerCart($customer->id, true);

        $this->assertDatabaseMissing('cart', [
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function it_clears_guest_cart_by_session_id()
    {
        $product = Product::factory()->create();

        Cart::create([
            'session_id' => 'test-session-123',
            'item_type' => Product::class,
            'item_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->checkoutService->clearCustomerCart('test-session-123', false);

        $this->assertDatabaseMissing('cart', [
            'session_id' => 'test-session-123',
        ]);
    }

    /** @test */
    public function it_sends_order_confirmation_email()
    {
        Mail::fake();

        $customer = Customer::factory()->create(['email' => 'customer@example.com']);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->checkoutService->sendOrderConfirmationEmail($order);

        Mail::assertSent(OrderConfirmationMail::class, function ($mail) use ($customer, $order) {
            return $mail->hasTo($customer->email) && $mail->order->id === $order->id;
        });
    }

    /** @test */
    public function it_logs_error_when_order_confirmation_email_fails()
    {
        Mail::shouldReceive('to')
            ->once()
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('SMTP connection failed'));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Order confirmation email failed to send'
                    && isset($context['order_id'])
                    && isset($context['customer_email'])
                    && isset($context['error']);
            });

        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        // Should not throw exception
        $this->checkoutService->sendOrderConfirmationEmail($order);
    }

    /** @test */
    public function it_sends_account_claim_email_to_guest_customers()
    {
        Mail::fake();

        $customer = Customer::factory()->create([
            'email' => 'guest@example.com',
            'password' => null, // Guest customer
        ]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->checkoutService->sendAccountClaimEmail($customer, $order);

        Mail::assertSent(ClaimAccountMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    /** @test */
    public function it_does_not_send_claim_email_to_customers_with_password()
    {
        Mail::fake();

        $customer = Customer::factory()->create([
            'password' => bcrypt('password'), // Has password
        ]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->checkoutService->sendAccountClaimEmail($customer, $order);

        Mail::assertNotSent(ClaimAccountMail::class);
    }

    /** @test */
    public function it_logs_error_when_claim_email_fails()
    {
        Mail::shouldReceive('to')
            ->once()
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Email send failed'));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Account claim email failed to send'
                    && isset($context['customer_id'])
                    && isset($context['order_id'])
                    && isset($context['error']);
            });

        $customer = Customer::factory()->create(['password' => null]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        // Should not throw exception
        $this->checkoutService->sendAccountClaimEmail($customer, $order);
    }

    /** @test */
    public function it_returns_authenticated_user_when_logged_in()
    {
        $customer = Customer::factory()->create();
        Auth::login($customer);

        $result = $this->checkoutService->findOrCreateCustomer([
            'email' => 'different@example.com',
            'name' => 'Different Name',
        ]);

        $this->assertEquals($customer->id, $result->id);
    }

    /** @test */
    public function it_creates_new_customer_for_guest_checkout()
    {
        $validated = [
            'email' => 'newguest@example.com',
            'name' => 'New Guest',
            'phone' => '555-1234',
        ];

        $customer = $this->checkoutService->findOrCreateCustomer($validated);

        $this->assertDatabaseHas('customers', [
            'email' => 'newguest@example.com',
            'name' => 'New Guest',
            'phone' => '555-1234',
            'password' => null,
        ]);
    }

    /** @test */
    public function it_finds_existing_customer_by_email_for_guest_checkout()
    {
        $existing = Customer::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Original Name',
        ]);

        $validated = [
            'email' => 'existing@example.com',
            'name' => 'New Name', // Should not update
        ];

        $customer = $this->checkoutService->findOrCreateCustomer($validated);

        $this->assertEquals($existing->id, $customer->id);
        $this->assertEquals('Original Name', $customer->name); // Should keep original
    }

    /** @test */
    public function it_gets_cart_items_for_authenticated_customer()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        Cart::factory()->forProduct($product)->create([
            'customer_id' => $customer->id,
            'quantity' => 3,
        ]);

        $cartItems = $this->checkoutService->getCartItems($customer->id, true);

        $this->assertCount(1, $cartItems);
        $this->assertEquals($customer->id, $cartItems->first()->customer_id);
        $this->assertEquals(3, $cartItems->first()->quantity);
        $this->assertNotNull($cartItems->first()->item); // Eager loaded
    }

    /** @test */
    public function it_gets_cart_items_for_guest_by_session_id()
    {
        $product = Product::factory()->create();

        Cart::factory()->forProduct($product)->create([
            'session_id' => 'guest-session-456',
            'quantity' => 2,
        ]);

        $cartItems = $this->checkoutService->getCartItems('guest-session-456', false);

        $this->assertCount(1, $cartItems);
        $this->assertEquals('guest-session-456', $cartItems->first()->session_id);
        $this->assertEquals(2, $cartItems->first()->quantity);
        $this->assertNotNull($cartItems->first()->item); // Eager loaded
    }

    /** @test */
    public function it_handles_empty_cart()
    {
        $customer = Customer::factory()->create();

        $cartItems = $this->checkoutService->getCartItems($customer->id, true);

        $this->assertCount(0, $cartItems);
    }
}
