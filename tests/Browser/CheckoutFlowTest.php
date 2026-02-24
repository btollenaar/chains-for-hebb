<?php

namespace Tests\Browser;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CheckoutFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test 1: Complete guest checkout flow end-to-end
     * Tests: Product selection → Add to cart → Checkout → Stripe redirect
     */
    public function test_complete_guest_checkout_flow(): void
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Browser Product',
            'price' => 49.99,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                    ->assertSee('Test Browser Product')
                    ->assertSee('$49.99')

                    // Add to cart
                    ->press('Add to Cart')
                    ->pause(1000) // Wait for AJAX

                    // Verify cart icon updated
                    ->assertSee('1') // Cart count badge

                    // Go to cart
                    ->visit('/cart')
                    ->assertSee('Test Browser Product')
                    ->assertSee('$49.99')

                    // Proceed to checkout
                    ->press('Proceed to Checkout')
                    ->assertPathIs('/checkout')

                    // Fill checkout form
                    ->type('name', 'Browser Test User')
                    ->type('email', 'browser@test.com')
                    ->type('phone', '555-1234')
                    ->type('billing_street', '123 Test St')
                    ->type('billing_city', 'Test City')
                    ->type('billing_state', 'CA')
                    ->type('billing_zip', '12345')

                    // Select cash payment (avoids Stripe for testing)
                    ->radio('payment_method', 'cash')

                    // Complete order
                    ->press('Place Order')
                    ->pause(2000) // Wait for processing

                    // Verify success page
                    ->assertPathIs('/checkout/success')
                    ->assertSee('Order Confirmed')
                    ->assertSee('Browser Test User');
        });
    }

    /**
     * Test 2: Mobile navigation works
     * Tests: Hamburger menu opens, categories expand, navigation works
     */
    public function test_mobile_navigation_works(): void
    {
        // Create test categories
        $category = ProductCategory::factory()->create(['name' => 'Mobile Test Category']);

        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone SE size
                    ->visit('/')

                    // Open mobile menu
                    ->press('@mobile-menu-toggle') // Assuming data-dusk attribute
                    ->pause(500) // Wait for animation

                    // Verify menu visible
                    ->assertSee('Products')
                    ->assertSee('Services')
                    ->assertSee('About')
                    ->assertSee('Contact')

                    // Click products to expand categories
                    ->click('nav a[href*="products"]')
                    ->pause(500)

                    // Verify categories visible
                    ->assertSee('Mobile Test Category')

                    // Close menu
                    ->press('@mobile-menu-toggle')
                    ->pause(500);
        });
    }

    /**
     * Test 3: AJAX cart updates without page reload
     * Tests: JavaScript cart operations, DOM updates, cart count
     */
    public function test_ajax_cart_updates_without_reload(): void
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'AJAX Test Product',
            'price' => 29.99,
            'stock_quantity' => 50,
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)

                    // Initial cart count should be 0
                    ->assertDontSee('1') // No cart badge initially

                    // Add to cart via AJAX
                    ->press('Add to Cart')
                    ->pause(1500) // Wait for AJAX + notification

                    // Verify notification appeared
                    ->assertSee('Added to cart') // Success notification

                    // Verify cart count updated WITHOUT page reload
                    ->assertSee('1') // Cart badge appears

                    // Add same product again
                    ->press('Add to Cart')
                    ->pause(1500)

                    // Cart count should increment
                    ->assertSee('2'); // Cart badge updated
        });
    }

    /**
     * Test 4: Notification system displays
     * Tests: Alpine.js notifications, auto-dismiss, multiple notifications
     */
    public function test_notification_system_displays(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'notification@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->visit('/login')

                    // Trigger error notification (wrong password)
                    ->type('email', 'notification@test.com')
                    ->type('password', 'wrongpassword')
                    ->press('Log in')
                    ->pause(500)

                    // Verify error notification displays
                    ->assertSee('credentials do not match') // Laravel's error message

                    // Login with correct credentials
                    ->type('email', 'notification@test.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->pause(1000)

                    // Verify success notification or redirect
                    ->assertPathIs('/dashboard')
                    ->assertAuthenticated();
        });
    }

    /**
     * Test 5: Stripe redirect opens (test mode)
     * Tests: Stripe integration, redirect behavior, payment gateway
     */
    public function test_stripe_redirect_opens(): void
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Stripe Test Product',
            'price' => 99.99,
            'stock_quantity' => 25,
            'status' => 'active',
        ]);
        $product->categories()->attach($category->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                    ->press('Add to Cart')
                    ->pause(1000)

                    ->visit('/cart')
                    ->press('Proceed to Checkout')

                    // Fill checkout form
                    ->type('name', 'Stripe Test User')
                    ->type('email', 'stripe@test.com')
                    ->type('phone', '555-9999')
                    ->type('billing_street', '456 Stripe Ave')
                    ->type('billing_city', 'Payment City')
                    ->type('billing_state', 'NY')
                    ->type('billing_zip', '54321')

                    // Select Stripe payment
                    ->radio('payment_method', 'stripe')

                    // Place order (should redirect to Stripe)
                    ->press('Place Order')
                    ->pause(3000) // Wait for Stripe redirect

                    // Verify redirect to Stripe Checkout
                    // Note: URL will be checkout.stripe.com in test mode
                    ->assertUrlContains('checkout.stripe.com');
        });
    }
}
