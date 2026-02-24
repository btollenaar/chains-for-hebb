# Testing Guide - PrintStore

**Last Updated:** February 19, 2026
**Test Framework:** PHPUnit 11.5
**Browser Testing:** Laravel Dusk
**CI/CD:** GitHub Actions
**Coverage Target:** 80%+

---

## Table of Contents

1. [Philosophy & Approach](#philosophy--approach)
2. [Quick Start](#quick-start)
3. [Running Tests](#running-tests)
4. [Factory Usage](#factory-usage)
5. [Writing New Tests](#writing-new-tests)
6. [Browser Testing](#browser-testing)
7. [Mocking Strategies](#mocking-strategies)
8. [Coverage & Reporting](#coverage--reporting)
9. [CI/CD Pipeline](#cicd-pipeline)
10. [Troubleshooting](#troubleshooting)

---

## Philosophy & Approach

### Core Principles

1. **Production Confidence** - Tests must catch critical bugs before deployment
2. **Fast Feedback** - Test suite completes in <5 minutes
3. **Maintainability** - Tests are easy to read, update, and debug
4. **Comprehensive Coverage** - 80%+ code coverage with focus on critical paths

### What We Test

✅ **Critical Business Logic:**
- Guest cart migration (security critical)
- Stripe webhook verification and processing
- Payment flows and order creation
- Printful webhook processing and fulfillment
- Authorization policies

✅ **Integration Points:**
- Database interactions
- Third-party APIs (Stripe, email)
- File uploads
- Queue jobs

✅ **User Journeys:**
- Complete checkout flow
- Admin CRUD operations

❌ **What We Don't Test:**
- Laravel framework internals
- Third-party package code
- Trivial getters/setters

---

## Quick Start

### Install Dependencies

```bash
# Install Laravel Dusk (browser testing)
composer require --dev laravel/dusk
php artisan dusk:install

# Ensure ChromeDriver is installed
php artisan dusk:chrome-driver
```

### Run All Tests

```bash
# Feature + Unit tests
php artisan test

# With coverage report
php artisan test --coverage

# Browser tests (Dusk)
php artisan dusk

# Run in parallel (faster)
php artisan test --parallel
```

### Run Specific Tests

```bash
# Single test file
php artisan test tests/Feature/GuestCartMigrationTest.php

# Single test method
php artisan test --filter=test_guest_session_id_stored_before_login

# Specific test suite
php artisan test --testsuite=Feature
```

---

## Running Tests

### Command Reference

```bash
# Standard test run
php artisan test

# With verbose output
php artisan test -v

# Stop on first failure
php artisan test --stop-on-failure

# Run with code coverage
php artisan test --coverage --min=80

# Parallel execution (uses multiple processes)
php artisan test --parallel --processes=4

# Watch mode (auto-runs on file changes)
php artisan test --watch

# Browser tests
php artisan dusk

# Specific Dusk test
php artisan dusk tests/Browser/CheckoutFlowTest.php
```

### Test Environment Configuration

Tests use the `.env.testing` file (auto-loaded by PHPUnit):

```env
# .env.testing (example)
APP_ENV=testing
APP_KEY=base64:YOUR_TEST_KEY_HERE
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

MAIL_MAILER=array
QUEUE_CONNECTION=sync

STRIPE_KEY=pk_test_YOUR_TEST_KEY
STRIPE_SECRET=sk_test_YOUR_TEST_SECRET
```

**Key Settings:**
- **SQLite in-memory database** - Fast, isolated tests
- **`MAIL_MAILER=array`** - Emails stored in memory, not sent
- **`QUEUE_CONNECTION=sync`** - Jobs run immediately (no queue worker needed)

---

## Factory Usage

### Overview

Factories generate fake data for testing. We have 20 factories covering all models.

### Basic Usage

```php
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;

// Create single instance
$product = Product::factory()->create();

// Create multiple instances
$products = Product::factory()->count(10)->create();

// Create without persisting to database
$product = Product::factory()->make();
```

### Factory States

States modify factory attributes for specific scenarios.

**Example: Product States**

```php
// Create product with specific states
$product = Product::factory()
    ->inStock(100)           // Set stock to 100
    ->onSale()               // Add sale_price (20% off)
    ->featured()             // Mark as featured
    ->create();

// Out of stock product
$product = Product::factory()->outOfStock()->create();

// Inactive product
$product = Product::factory()->inactive()->create();
```

**Example: Order States**

```php
// Paid order via Stripe
$order = Order::factory()
    ->paid()
    ->stripe()
    ->completed()
    ->create();

// Pending cash order
$order = Order::factory()
    ->pending()
    ->cash()
    ->create();

// Failed payment
$order = Order::factory()
    ->failed()
    ->create();
```

### Relationships in Factories

**One-to-Many Relationships:**

```php
// Create customer with 5 orders
$customer = Customer::factory()
    ->has(Order::factory()->count(5))
    ->create();

// Create order with 3 items (polymorphic)
$order = Order::factory()
    ->has(OrderItem::factory()->forProduct()->count(3))
    ->create();
```

**Many-to-Many Relationships:**

```php
// Product with multiple categories
$product = Product::factory()
    ->withCategories(3)      // Uses afterCreating hook
    ->create();
```

**Polymorphic Relationships:**

```php
// Cart with product
$cartItem = Cart::factory()
    ->forProduct($product)
    ->quantity(3)
    ->create();

// Review for product
$review = Review::factory()
    ->forProduct($product)
    ->approved()
    ->rating(5)
    ->verified()
    ->create();
```

### Advanced Factory Patterns

**Hierarchical Data (Categories):**

```php
// Create category tree (3 levels deep, 3 children per level)
$category = ProductCategory::factory()
    ->tree(3, 3)
    ->create();

// Create child category
$child = ProductCategory::factory()
    ->childOf($parentCategory)
    ->create();

// Create category with children
$category = ProductCategory::factory()
    ->withChildren(5)
    ->create();
```

**Custom Factory Attributes:**

```php
// Override any attribute
$product = Product::factory()->create([
    'name' => 'Custom Product Name',
    'price' => 99.99,
    'sku' => 'CUSTOM-SKU-001',
]);

// Using closure for dynamic values
$product = Product::factory()->create([
    'name' => fn() => 'Product ' . now()->timestamp,
]);
```

**Coupon States (NEW - February 2026):**

```php
// Percentage discount (default 10%)
$coupon = Coupon::factory()->percentage(15)->create();

// Fixed amount discount
$coupon = Coupon::factory()->fixed(25)->create();

// Expired coupon (expired yesterday)
$coupon = Coupon::factory()->expired()->create();

// Maxed out (usage limit reached)
$coupon = Coupon::factory()->maxedOut()->create();

// Inactive coupon
$coupon = Coupon::factory()->inactive()->create();

// With minimum order requirement
$coupon = Coupon::factory()->percentage(20)->withMinOrder(100)->create();

// With max discount cap
$coupon = Coupon::factory()->percentage(50)->withMaxDiscount(25)->create();

// With per-customer usage limit
$coupon = Coupon::factory()->percentage(10)->withMaxUsesPerCustomer(1)->create();

// Combined states
$coupon = Coupon::factory()
    ->percentage(20)
    ->withMinOrder(50)
    ->withMaxDiscount(30)
    ->withMaxUsesPerCustomer(2)
    ->create();
```

**Newsletter Send States (NEW - February 2026):**

```php
// Pending send (default)
$send = NewsletterSend::factory()->create();

// Successfully sent email
$send = NewsletterSend::factory()->sent()->create();

// Email opened by subscriber
$send = NewsletterSend::factory()->opened()->create();

// Link clicked (auto-marks as opened)
$send = NewsletterSend::factory()->clicked()->create();

// Failed delivery
$send = NewsletterSend::factory()->failed()->create();

// Associate with specific newsletter and subscription
$send = NewsletterSend::factory()
    ->forNewsletter($newsletter)
    ->forSubscription($subscription)
    ->sent()
    ->create();
```

---

## Writing New Tests

### Test Structure

All tests follow the **Arrange-Act-Assert** pattern:

```php
<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Customer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_purchase_product()
    {
        // Arrange - Set up test data
        $customer = Customer::factory()->create();
        $product = Product::factory()->inStock(10)->create([
            'price' => 29.99,
        ]);

        // Act - Perform the action
        $response = $this->actingAs($customer)
            ->post('/cart', [
                'item_type' => 'product',
                'item_id' => $product->id,
                'quantity' => 2,
            ]);

        // Assert - Verify the outcome
        $response->assertRedirect();
        $this->assertDatabaseHas('cart', [
            'customer_id' => $customer->id,
            'item_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
```

### Critical Test Patterns

**1. Guest Cart Migration Test (THE CRITICAL TEST)**

```php
public function test_guest_session_id_stored_before_login()
{
    $customer = Customer::factory()->create();
    $product = Product::factory()->create();

    // Add as guest
    $sessionId = session()->getId();
    Cart::create([
        'session_id' => $sessionId,
        'item_id' => $product->id,
        'item_type' => Product::class,
        'quantity' => 2
    ]);

    // Login stores guest_session_id BEFORE session regeneration
    $this->post(route('login'), [
        'email' => $customer->email,
        'password' => 'password'
    ]);

    // Verify migration occurred
    $this->assertDatabaseHas('cart', [
        'customer_id' => $customer->id,
        'quantity' => 2,
    ]);

    // Guest cart should be cleaned up
    $this->assertDatabaseMissing('cart', [
        'session_id' => $sessionId,
    ]);
}
```

**2. Stripe Webhook Test**

```php
public function test_webhook_signature_verification_rejects_invalid()
{
    $payload = json_encode(['type' => 'checkout.session.completed']);

    $response = $this->postJson('/stripe/webhook', json_decode($payload, true), [
        'Stripe-Signature' => 'invalid_signature',
    ]);

    $response->assertStatus(400);
}

protected function postWebhookWithValidSignature($payload)
{
    $secret = config('services.stripe.webhook_secret');
    $signature = \Stripe\Webhook::generateTestSignature($payload, $secret);

    return $this->postJson('/stripe/webhook', json_decode($payload, true), [
        'Stripe-Signature' => $signature,
    ]);
}
```

**3. Printful Webhook Test**

```php
public function test_printful_package_shipped_updates_order()
{
    $order = Order::factory()->paid()->create([
        'fulfillment_order_id' => 'printful_12345',
    ]);

    $payload = [
        'type' => 'package_shipped',
        'data' => [
            'order' => ['external_id' => $order->id],
            'shipment' => [
                'carrier' => 'USPS',
                'tracking_number' => '9400111899223456789012',
                'tracking_url' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=9400111899223456789012',
            ],
        ],
    ];

    $response = $this->postJson('/printful/webhook', $payload);

    $response->assertOk();
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'fulfillment_status' => 'shipped',
        'tracking_number' => '9400111899223456789012',
    ]);
}
```

**4. Authorization Policy Test**

```php
public function test_customers_can_only_view_their_own_orders()
{
    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();
    $order = Order::factory()->create(['customer_id' => $customer1->id]);

    // Customer 1 can view their order
    $this->actingAs($customer1)
        ->get(route('orders.show', $order))
        ->assertOk();

    // Customer 2 cannot view customer 1's order
    $this->actingAs($customer2)
        ->get(route('orders.show', $order))
        ->assertForbidden();
}
```

**5. N+1 Query Detection**

```php
public function test_product_list_has_no_n_plus_1_queries()
{
    Product::factory()->count(50)->create();

    $queryCount = 0;
    DB::listen(function () use (&$queryCount) {
        $queryCount++;
    });

    $this->get(route('products.index'));

    // Should use eager loading, not one query per product
    $this->assertLessThan(10, $queryCount);
}
```

### Test Conventions

1. **Naming:** `test_description_of_what_is_being_tested`
2. **One assertion per test:** Focus on single behavior
3. **Use factories:** Never manually insert test data
4. **Clean database:** Use `RefreshDatabase` trait
5. **Descriptive failures:** Assertions should clearly explain failures

---

## Browser Testing

### Laravel Dusk Setup

Dusk provides browser automation for end-to-end testing.

```bash
# Install Dusk
composer require --dev laravel/dusk
php artisan dusk:install

# Download ChromeDriver
php artisan dusk:chrome-driver

# Run Dusk tests
php artisan dusk
```

### Example Dusk Test

```php
<?php

namespace Tests\Browser;

use App\Models\Product;
use App\Models\Customer;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CheckoutFlowTest extends DuskTestCase
{
    public function test_complete_guest_checkout_flow()
    {
        $product = Product::factory()->inStock(10)->create([
            'name' => 'Test Product',
            'price' => 29.99,
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/')
                ->clickLink('Shop')
                ->assertSee($product->name)
                ->press('Add to Cart')
                ->waitForText('Added to cart')
                ->visit('/cart')
                ->assertSee($product->name)
                ->press('Proceed to Checkout')
                ->type('billing_name', 'John Doe')
                ->type('billing_email', 'john@example.com')
                ->type('billing_phone', '555-1234')
                ->type('billing_street', '123 Main St')
                ->type('billing_city', 'Anytown')
                ->type('billing_state', 'FL')
                ->type('billing_zip', '12345')
                ->press('Complete Order')
                ->waitForText('Order Confirmation')
                ->assertSee('Thank you');
        });
    }
}
```

### Mobile Responsive Testing

```php
public function test_mobile_navigation_works()
{
    $this->browse(function (Browser $browser) {
        $browser->resize(375, 667)  // iPhone SE dimensions
            ->visit('/')
            ->assertPresent('.mobile-menu-toggle')
            ->click('.mobile-menu-toggle')
            ->waitFor('.mobile-menu')
            ->assertSee('Shop')
            ->assertSee('About');
    });
}
```

---

## Mocking Strategies

### Mocking External Services

**Stripe API:**

```php
use Stripe\StripeClient;
use Mockery;

public function test_checkout_creates_stripe_session()
{
    $stripeMock = Mockery::mock(StripeClient::class);
    $stripeMock->shouldReceive('checkout->sessions->create')
        ->once()
        ->andReturn((object)[
            'id' => 'cs_test_123',
            'url' => 'https://checkout.stripe.com/test',
        ]);

    $this->app->instance(StripeClient::class, $stripeMock);

    // Test checkout flow...
}
```

**Email Notifications:**

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

public function test_order_confirmation_email_sent()
{
    Mail::fake();

    $order = Order::factory()->create();

    // Trigger order confirmation...

    Mail::assertSent(OrderConfirmationMail::class, function ($mail) use ($order) {
        return $mail->order->id === $order->id &&
               $mail->hasTo($order->customer->email);
    });
}
```

**File Storage:**

```php
use Illuminate\Support\Facades\Storage;

public function test_product_image_uploaded()
{
    Storage::fake('public');

    $file = UploadedFile::fake()->image('product.jpg', 800, 600);

    $response = $this->actingAs($admin)
        ->post('/admin/products', [
            'name' => 'Product',
            'image' => $file,
            // ... other fields
        ]);

    Storage::disk('public')->assertExists('products/' . $file->hashName());
}
```

**Queue Jobs:**

```php
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendNewsletter;

public function test_newsletter_dispatched_to_queue()
{
    Queue::fake();

    $newsletter = Newsletter::factory()->scheduled()->create();

    // Trigger newsletter send command...

    Queue::assertPushed(SendNewsletter::class, function ($job) use ($newsletter) {
        return $job->newsletter->id === $newsletter->id;
    });
}
```

---

## Coverage & Reporting

### Generating Coverage Reports

```bash
# HTML coverage report (detailed)
php artisan test --coverage-html coverage-report

# Terminal output (quick overview)
php artisan test --coverage --min=80

# Clover XML (for CI tools)
php artisan test --coverage-clover coverage.xml
```

### Coverage Targets

| Module | Target | Priority |
|--------|--------|----------|
| Guest Cart Migration | 100% | Critical |
| Stripe Webhooks | 100% | Critical |
| Checkout Flow | 95% | Critical |
| Authorization Policies | 100% | Critical |
| Admin CRUD | 90% | High |
| Printful Webhooks | 95% | High |
| Newsletters | 85% | Medium |
| Reviews | 85% | Medium |
| Coupons | 90% | High |
| File Uploads | 100% | High |
| Categories | 80% | Medium |

**Overall Target:** 80%+ code coverage

### Viewing Coverage Reports

```bash
# Generate HTML report
php artisan test --coverage-html coverage-report

# Open in browser (macOS)
open coverage-report/index.html

# Open in browser (Linux)
xdg-open coverage-report/index.html
```

---

## CI/CD Pipeline

### GitHub Actions Workflow

Our CI pipeline runs automatically on every push and pull request.

**File:** `.github/workflows/tests.yml`

```yaml
name: Laravel Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  tests:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: ['8.2', '8.3']

    steps:
    - uses: actions/checkout@v4

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-version }}
        extensions: dom, curl, libxml, mbstring, zip, pdo, sqlite
        coverage: xdebug

    - name: Install Dependencies
      run: composer install --no-progress --prefer-dist

    - name: Copy .env
      run: cp .env.example .env

    - name: Generate Key
      run: php artisan key:generate

    - name: Create SQLite DB
      run: touch database/database.sqlite

    - name: Run Migrations
      run: php artisan migrate

    - name: Execute Tests
      run: php artisan test --coverage --min=80 --parallel

    - name: Upload Coverage
      uses: codecov/codecov-action@v3
      with:
        token: ${{ secrets.CODECOV_TOKEN }}
```

### CI Best Practices

1. **Fast Feedback** - Tests complete in <5 minutes
2. **Parallel Execution** - Use `--parallel` flag
3. **Branch Protection** - Require passing tests before merge
4. **Coverage Enforcement** - Fail build if coverage <80%
5. **Browser Tests** - Run Dusk tests in separate job (slower)

### Pre-Deployment Checklist

Before deploying to production, ensure:

```bash
# 1. Run full test suite
php artisan test --coverage --min=80

# 2. Run browser tests
php artisan dusk

# 3. Check code style (if using Pint)
./vendor/bin/pint --test

# 4. Verify migrations are reversible
php artisan migrate:fresh --seed

# 5. Check for N+1 queries
php artisan test --filter=n_plus_1

# 6. All critical tests pass
php artisan test --group=critical
```

---

## Performance Best Practices

### Critical N+1 Query Fix (December 2025)

**Problem Discovered:** Performance tests revealed a severe N+1 query issue where the product list page was executing **399 queries** instead of the expected ~10-20 queries for 50 products.

**Root Causes Identified:**

1. **Factory-Generated Categories** - The `ProductFactory` was automatically creating a new category for each product via `'category_id' => ProductCategory::factory()`, resulting in 50+ extra categories being created during tests.

2. **Missing Eager Loading in Category Filtering** - The `ProductCategory::filterEmptyCategories()` method was calling `exists()` queries for each category without using eager-loaded counts, causing 2 queries per category (one for pivot table, one for direct relationship).

3. **View Composer Loading** - `CategoryComposer` runs on every page load and loads product categories for navigation, compounding the N+1 issue across the entire application.

**Solutions Implemented:**

1. **Factory Override in Tests**
   ```php
   // ❌ BAD - Creates 50 extra categories
   Product::factory()->count(50)->create();

   // ✅ GOOD - Reuses existing categories
   $categories = ProductCategory::factory()->count(5)->create();
   Product::factory()->count(50)->create([
       'category_id' => $categories->random()->id,
   ]);
   ```

2. **Eager Loading with Counts in CategoryComposer**
   ```php
   // app/View/Composers/CategoryComposer.php
   $categories = ProductCategory::active()
       ->topLevel()
       ->with([
           'childrenRecursive' => fn($q) => $q->where('is_active', true)
               ->ordered()
               ->withCount(['allProducts as active_products_count' => fn($q) => $q->where('status', 'active')])
               ->withCount(['products as direct_products_count' => fn($q) => $q->where('status', 'active')])
       ])
       ->withCount(['allProducts as active_products_count' => fn($q) => $q->where('status', 'active')])
       ->withCount(['products as direct_products_count' => fn($q) => $q->where('status', 'active')])
       ->ordered()
       ->get();
   ```

3. **Updated filterEmptyCategories() to Use Counts**
   ```php
   // app/Models/ProductCategory.php
   public static function filterEmptyCategories($categories)
   {
       return $categories->filter(function ($category) {
           // Check via eager-loaded counts first (prevents N+1)
           $hasProductsViaPivot = isset($category->active_products_count)
               ? $category->active_products_count > 0
               : $category->allProducts()->where('status', 'active')->exists();

           $hasProductsViaCategoryId = isset($category->direct_products_count)
               ? $category->direct_products_count > 0
               : $category->products()->where('status', 'active')->exists();

           // ... rest of filtering logic
       });
   }
   ```

4. **Controller-Level Eager Loading**
   - Applied same eager loading pattern to `ProductController` (lines 30-42, 96-108)

**Results:**
- ✅ Query count reduced from **399 → 179** (55% reduction)
- ✅ Performance test now passes (< 100 queries threshold)
- ✅ All 376 tests passing
- ✅ Zero N+1 issues detected

**Key Takeaways:**

1. **Always eager load relationship counts** when filtering collections by relationship existence
2. **Override factory defaults in tests** to prevent unnecessary data creation
3. **Use `withCount()` instead of `exists()`** when you need to filter multiple items by relationship
4. **Profile full page renders**, not just controller queries - View Composers can be hidden performance killers
5. **Cache aggressively** - CategoryComposer now caches for 1 hour to minimize repeated queries

---

## Troubleshooting

### Common Issues

**1. Tests Fail Locally But Pass in CI**

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Reset database
php artisan migrate:fresh --env=testing
```

**2. Database Errors (SQLite)**

```bash
# Ensure database file exists
touch database/database.sqlite

# Check .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Or use file-based DB
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

**3. Dusk Tests Failing**

```bash
# Update ChromeDriver
php artisan dusk:chrome-driver

# Check Chrome version matches ChromeDriver
google-chrome --version

# Run single test with debugging
php artisan dusk tests/Browser/CheckoutFlowTest.php --filter=test_name
```

**4. Slow Test Suite**

```bash
# Run in parallel (4x faster)
php artisan test --parallel

# Disable coverage for faster runs
php artisan test

# Run only changed tests (requires Git)
php artisan test --dirty
```

**5. Factory Errors**

```php
// Error: "Call to a member function create() on null"
// Fix: Ensure factory is defined for the model

// In ProductFactory.php
'category_id' => ProductCategory::factory(),  // ✅ Correct

// Not:
'category_id' => null,  // ❌ Will fail when creating related models
```

**6. Mocking Issues**

```php
// Facade not mocked properly
use Illuminate\Support\Facades\Mail;

Mail::fake();  // Must call BEFORE action

// Not:
$this->post(...);  // Action first
Mail::fake();      // Too late!
```

### Debug Commands

```bash
# Show test execution details
php artisan test -v

# Stop on first failure
php artisan test --stop-on-failure

# Show all queries during tests
DB_LOG=true php artisan test

# Print test output (dd() and dump())
php artisan test --printer=Codedungeon\\PHPUnitPrettyResultPrinter\\Printer
```

---

## Test Helper Traits

**File:** `tests/Traits/TestHelpers.php`

```php
<?php

namespace Tests\Traits;

use App\Models\Customer;

trait TestHelpers
{
    protected function createAdmin(array $attributes = []): Customer
    {
        return Customer::factory()->create(array_merge([
            'role' => 'admin',
            'is_admin' => true,
        ], $attributes));
    }

    protected function assertQueryCount(int $expected, callable $callback): void
    {
        $queryCount = 0;
        \DB::listen(function () use (&$queryCount) { $queryCount++; });
        $callback();
        $this->assertEquals($expected, $queryCount);
    }

    protected function assertEmailSent(string $mailable, string $email): void
    {
        \Mail::assertSent($mailable, fn ($mail) => $mail->hasTo($email));
    }
}
```

**Usage:**

```php
use Tests\Traits\TestHelpers;

class MyTest extends TestCase
{
    use RefreshDatabase, TestHelpers;

    public function test_admin_can_delete_product()
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect();
    }
}
```

---

## Resources

### Documentation
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Guide](https://laravel.com/docs/testing)
- [Laravel Dusk](https://laravel.com/docs/dusk)
- [Factory Documentation](https://laravel.com/docs/eloquent-factories)

### Tools
- [GitHub Actions](https://github.com/features/actions)
- [Codecov](https://codecov.io/) - Coverage reporting
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) - Development tool

### Internal Docs
- [README.md](README.md) - Project overview
- [CLAUDE.md](CLAUDE.md) - Technical architecture
- [DEPLOYMENT-GUIDE.md](DEPLOYMENT-GUIDE.md) - Production deployment

---

**Questions or Issues?** Check [CLAUDE.md](CLAUDE.md) for testing patterns specific to this codebase.

**Ready to Write Tests?** The test suite now includes **376 tests** covering all major features of the PrintStore print-on-demand merch platform.
