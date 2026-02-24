# 05 - Tech Integration Plan

**Last Updated:** February 12, 2026

---

## Overview

The existing Laravel 11 platform provides a strong foundation. This document outlines the API integrations, modifications, and automation needed to support a live e-commerce business with dropshipping fulfillment.

### Integration Priority

| Integration | Priority | Phase | Effort | Impact |
|-------------|----------|-------|--------|--------|
| Printful API | Critical | 1 | Medium (2-3 days) | Automated fulfillment for branded products |
| CJDropshipping API | Critical | 1 | Medium (2-3 days) | Automated fulfillment for eco products |
| Google Analytics 4 | Critical | 1 | Low (2-4 hours) | Traffic and conversion tracking |
| Google Shopping Feed | High | 1 | Medium (1-2 days) | Product listing ads |
| Meta Pixel | High | 1 | Low (2-4 hours) | Retargeting, conversion tracking |
| Email Automation Flows | High | 1-2 | Medium (2-3 days) | Welcome, abandoned cart, post-purchase |
| TaxJar API | Medium | 2 | Medium (1-2 days) | Automated tax calculation |
| Inventory Sync | Medium | 2 | Medium (1-2 days) | Cross-supplier stock management |
| ShipBob API | Low | 3 | Medium (2-3 days) | 3PL fulfillment when scaling |

---

## Printful API Integration

### Overview

Printful offers a comprehensive REST API for managing print-on-demand products. Perfect for custom-branded eco products (tote bags, t-shirts, accessories).

**API Documentation:** https://developers.printful.com/docs/

### Laravel Service Class

```php
// app/Services/PrintfulService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrintfulService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.printful.com';

    public function __construct()
    {
        $this->apiKey = config('services.printful.api_key');
    }

    /**
     * Create an order in Printful from a platform order
     */
    public function createOrder(Order $order, array $printfulItems): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/orders", [
                'recipient' => [
                    'name' => $order->shipping_address['name'],
                    'address1' => $order->shipping_address['street'],
                    'city' => $order->shipping_address['city'],
                    'state_code' => $order->shipping_address['state'],
                    'country_code' => $order->shipping_address['country'] ?? 'US',
                    'zip' => $order->shipping_address['zip'],
                ],
                'items' => $printfulItems,
                'external_id' => (string) $order->id,
            ]);

        if ($response->failed()) {
            Log::error('Printful order creation failed', [
                'order_id' => $order->id,
                'error' => $response->json(),
            ]);
            throw new \Exception('Printful order creation failed');
        }

        return $response->json()['result'];
    }

    /**
     * Get shipping rates for an order
     */
    public function getShippingRates(array $recipient, array $items): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/shipping/rates", [
                'recipient' => $recipient,
                'items' => $items,
            ]);

        return $response->json()['result'] ?? [];
    }

    /**
     * Get order status from Printful
     */
    public function getOrderStatus(string $externalId): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/orders/@{$externalId}");

        return $response->json()['result'] ?? [];
    }

    /**
     * Get available products catalog
     */
    public function getProducts(): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/store/products");

        return $response->json()['result'] ?? [];
    }
}
```

### Printful Webhook Handlers

```php
// app/Http/Controllers/PrintfulWebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PrintfulWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $event = $request->input('type');
        $data = $request->input('data');

        return match($event) {
            'package_shipped' => $this->handlePackageShipped($data),
            'order_failed' => $this->handleOrderFailed($data),
            'order_canceled' => $this->handleOrderCanceled($data),
            default => response()->json(['status' => 'unhandled']),
        };
    }

    private function handlePackageShipped(array $data): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($data['order']['external_id']);
        if (!$order) return response()->json(['status' => 'order_not_found'], 404);

        $order->update([
            'fulfillment_status' => 'shipped',
            'tracking_number' => $data['shipment']['tracking_number'] ?? null,
            'tracking_url' => $data['shipment']['tracking_url'] ?? null,
            'carrier' => $data['shipment']['carrier'] ?? null,
            'shipped_at' => now(),
        ]);

        // Send shipping notification email
        // Mail::to($order->customer->email)->send(new OrderShippedMail($order));

        return response()->json(['status' => 'processed']);
    }

    private function handleOrderFailed(array $data): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($data['order']['external_id']);
        if (!$order) return response()->json(['status' => 'order_not_found'], 404);

        $order->update(['fulfillment_status' => 'failed']);

        Log::error('Printful order fulfillment failed', [
            'order_id' => $order->id,
            'reason' => $data['reason'] ?? 'unknown',
        ]);

        return response()->json(['status' => 'processed']);
    }
}
```

### Config Entry

```php
// config/services.php (add to existing)
'printful' => [
    'api_key' => env('PRINTFUL_API_KEY'),
    'webhook_secret' => env('PRINTFUL_WEBHOOK_SECRET'),
],
```

---

## CJDropshipping Integration

### Overview

CJDropshipping provides an API for order placement, product search, and tracking. This is the primary fulfillment channel for non-printed eco products.

**API Documentation:** https://developers.cjdropshipping.com/

### Laravel Service Class

```php
// app/Services/CJDropshippingService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CJDropshippingService
{
    private string $apiKey;
    private string $baseUrl = 'https://developers.cjdropshipping.com/api/v2';
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->apiKey = config('services.cjdropshipping.api_key');
        $this->accessToken = $this->getAccessToken();
    }

    private function getAccessToken(): string
    {
        return Cache::remember('cj_access_token', 3600, function () {
            $response = Http::post("{$this->baseUrl}/authentication/getAccessToken", [
                'email' => config('services.cjdropshipping.email'),
                'password' => config('services.cjdropshipping.password'),
            ]);

            return $response->json()['data']['accessToken'];
        });
    }

    /**
     * Create order in CJDropshipping
     */
    public function createOrder(Order $order, array $cjItems): array
    {
        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/shopping/order/createOrder", [
                'orderNumber' => 'ORD-' . $order->id,
                'shippingZip' => $order->shipping_address['zip'],
                'shippingCountryCode' => $order->shipping_address['country'] ?? 'US',
                'shippingProvince' => $order->shipping_address['state'],
                'shippingCity' => $order->shipping_address['city'],
                'shippingAddress' => $order->shipping_address['street'],
                'shippingCustomerName' => $order->shipping_address['name'] ?? $order->customer->name,
                'shippingPhone' => $order->customer->phone ?? '',
                'products' => $cjItems,
            ]);

        return $response->json();
    }

    /**
     * Get tracking information for an order
     */
    public function getOrderTracking(string $orderNumber): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/shopping/order/getOrderDetail", [
                'orderNum' => $orderNumber,
            ]);

        return $response->json()['data'] ?? [];
    }

    /**
     * Search for products on CJ
     */
    public function searchProducts(string $query, int $page = 1): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/product/list", [
                'productNameEn' => $query,
                'pageNum' => $page,
                'pageSize' => 20,
            ]);

        return $response->json()['data'] ?? [];
    }

    /**
     * Get product variants and pricing
     */
    public function getProductDetail(string $pid): array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/product/query", [
                'pid' => $pid,
            ]);

        return $response->json()['data'] ?? [];
    }
}
```

### Config Entry

```php
// config/services.php (add to existing)
'cjdropshipping' => [
    'api_key' => env('CJ_API_KEY'),
    'email' => env('CJ_EMAIL'),
    'password' => env('CJ_PASSWORD'),
],
```

---

## Google Shopping Product Feed

### Overview

Generate a product feed that Google Merchant Center can ingest for Shopping Ads.

### Product Feed Generator

```php
// app/Services/GoogleShoppingFeedService.php

namespace App\Services;

use App\Models\Product;

class GoogleShoppingFeedService
{
    /**
     * Generate XML product feed for Google Merchant Center
     */
    public function generateFeed(): string
    {
        $products = Product::active()
            ->where('price', '>', 0)
            ->where('stock_quantity', '>', 0)
            ->with('primaryCategory')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">';
        $xml .= '<channel>';
        $xml .= '<title>' . config('app.name') . '</title>';
        $xml .= '<link>' . config('app.url') . '</link>';

        foreach ($products as $product) {
            $xml .= '<item>';
            $xml .= '<g:id>' . $product->id . '</g:id>';
            $xml .= '<g:title>' . htmlspecialchars($product->name) . '</g:title>';
            $xml .= '<g:description>' . htmlspecialchars(strip_tags($product->description)) . '</g:description>';
            $xml .= '<g:link>' . route('products.show', $product->slug) . '</g:link>';
            $xml .= '<g:image_link>' . $this->getProductImageUrl($product) . '</g:image_link>';
            $xml .= '<g:availability>' . ($product->stock_quantity > 0 ? 'in_stock' : 'out_of_stock') . '</g:availability>';
            $xml .= '<g:price>' . number_format($product->price, 2) . ' USD</g:price>';
            if ($product->compare_price) {
                $xml .= '<g:sale_price>' . number_format($product->price, 2) . ' USD</g:sale_price>';
            }
            $xml .= '<g:brand>' . config('app.name') . '</g:brand>';
            $xml .= '<g:condition>new</g:condition>';
            $xml .= '<g:product_type>' . htmlspecialchars($product->primaryCategory?->name ?? 'Home & Garden') . '</g:product_type>';
            $xml .= '<g:shipping_weight>' . ($product->weight ?? '1') . ' lb</g:shipping_weight>';
            $xml .= '</item>';
        }

        $xml .= '</channel></rss>';

        return $xml;
    }

    private function getProductImageUrl(Product $product): string
    {
        $images = $product->images;
        if (!empty($images) && is_array($images)) {
            return asset('storage/' . $images[0]);
        }
        return asset('images/placeholder.jpg');
    }
}
```

### Route & Controller

```php
// routes/web.php
Route::get('/feeds/google-shopping.xml', [FeedController::class, 'googleShopping']);

// app/Http/Controllers/FeedController.php
public function googleShopping(GoogleShoppingFeedService $feedService)
{
    $xml = Cache::remember('google_shopping_feed', 3600, function () use ($feedService) {
        return $feedService->generateFeed();
    });

    return response($xml, 200)->header('Content-Type', 'application/xml');
}
```

---

## Google Analytics 4 Integration

### Implementation

Add GA4 tracking to the main layout:

```blade
<!-- resources/views/layouts/app.blade.php (in <head>) -->
@if(config('services.google.analytics_id'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ config('services.google.analytics_id') }}');
</script>
@endif
```

### E-Commerce Event Tracking

```javascript
// Product View
gtag('event', 'view_item', {
    currency: 'USD',
    value: {{ $product->price }},
    items: [{
        item_id: '{{ $product->id }}',
        item_name: '{{ $product->name }}',
        price: {{ $product->price }},
    }]
});

// Add to Cart
gtag('event', 'add_to_cart', {
    currency: 'USD',
    value: price,
    items: [{ item_id: id, item_name: name, price: price, quantity: qty }]
});

// Begin Checkout
gtag('event', 'begin_checkout', {
    currency: 'USD',
    value: {{ $cartTotal }},
    items: [/* cart items */]
});

// Purchase Complete
gtag('event', 'purchase', {
    transaction_id: '{{ $order->id }}',
    value: {{ $order->total }},
    currency: 'USD',
    shipping: {{ $order->shipping_cost ?? 0 }},
    tax: {{ $order->tax ?? 0 }},
    items: [/* order items */]
});
```

---

## Meta Pixel Integration

```blade
<!-- resources/views/layouts/app.blade.php (in <head>) -->
@if(config('services.meta.pixel_id'))
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ config("services.meta.pixel_id") }}');
    fbq('track', 'PageView');
</script>
@endif
```

### Meta Pixel Events

```javascript
// View Content (Product Page)
fbq('track', 'ViewContent', {
    content_ids: ['{{ $product->id }}'],
    content_type: 'product',
    value: {{ $product->price }},
    currency: 'USD'
});

// Add to Cart
fbq('track', 'AddToCart', {
    content_ids: [id],
    content_type: 'product',
    value: price,
    currency: 'USD'
});

// Purchase
fbq('track', 'Purchase', {
    content_ids: [/* order item ids */],
    content_type: 'product',
    value: {{ $order->total }},
    currency: 'USD'
});
```

---

## TaxJar API Integration (Phase 2)

### Laravel Service Class

```php
// app/Services/TaxJarService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TaxJarService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.taxjar.com/v2';

    public function __construct()
    {
        $this->apiKey = config('services.taxjar.api_key');
    }

    /**
     * Calculate tax for an order
     */
    public function calculateTax(array $orderData): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/taxes", [
                'from_country' => 'US',
                'from_zip' => config('business.contact.zip'),
                'from_state' => config('business.contact.state'),
                'to_country' => $orderData['country'] ?? 'US',
                'to_zip' => $orderData['zip'],
                'to_state' => $orderData['state'],
                'to_city' => $orderData['city'],
                'amount' => $orderData['subtotal'],
                'shipping' => $orderData['shipping'],
                'line_items' => $orderData['items'],
            ]);

        return $response->json()['tax'] ?? [];
    }

    /**
     * Create a transaction for reporting
     */
    public function createTransaction(Order $order): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/transactions/orders", [
                'transaction_id' => (string) $order->id,
                'transaction_date' => $order->created_at->toDateString(),
                'to_zip' => $order->shipping_address['zip'],
                'to_state' => $order->shipping_address['state'],
                'amount' => $order->total,
                'shipping' => $order->shipping_cost ?? 0,
                'sales_tax' => $order->tax ?? 0,
            ]);

        return $response->json();
    }
}
```

---

## Email Automation Implementation

### Abandoned Cart Detection

The existing cart system tracks items by customer_id and session_id. Add a scheduled command to detect abandoned carts:

```php
// app/Console/Commands/SendAbandonedCartEmails.php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Mail\AbandonedCartMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmails extends Command
{
    protected $signature = 'cart:send-abandoned-emails';

    public function handle()
    {
        // Find carts with items added 1+ hours ago, customer has email, no order placed
        $abandonedCarts = Cart::select('customer_id')
            ->whereNotNull('customer_id')
            ->where('updated_at', '<', now()->subHour())
            ->where('updated_at', '>', now()->subDays(3))
            ->groupBy('customer_id')
            ->get();

        foreach ($abandonedCarts as $cart) {
            $customer = $cart->customer;
            if (!$customer || !$customer->email) continue;

            // Check if we already sent an abandoned cart email recently
            // (implement via a sent_emails tracking table or cache)

            try {
                Mail::to($customer->email)->send(new AbandonedCartMail($customer));
            } catch (\Exception $e) {
                Log::error('Abandoned cart email failed', [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

// routes/console.php
Schedule::command('cart:send-abandoned-emails')->hourly();
```

---

## Database Modifications for New Business

### New/Modified Columns Needed

```php
// Orders table additions
Schema::table('orders', function (Blueprint $table) {
    $table->string('fulfillment_status')->default('pending');
    // pending, processing, shipped, delivered, failed
    $table->string('fulfillment_provider')->nullable();
    // printful, cjdropshipping, manual
    $table->string('fulfillment_order_id')->nullable();
    // External order ID from fulfillment provider
    $table->string('tracking_number')->nullable();
    $table->string('tracking_url')->nullable();
    $table->string('carrier')->nullable();
    $table->timestamp('shipped_at')->nullable();
    $table->timestamp('delivered_at')->nullable();
});

// Products table additions
Schema::table('products', function (Blueprint $table) {
    $table->string('fulfillment_provider')->nullable();
    // Which provider handles this product
    $table->string('fulfillment_sku')->nullable();
    // SKU in fulfillment provider's system
    $table->decimal('wholesale_cost', 8, 2)->nullable();
    // Track COGS for margin calculation
    $table->decimal('weight_oz', 6, 2)->nullable();
    // Product weight for shipping calculation
});
```

### Fulfillment Provider Mapping

```php
// Products table will include fulfillment_provider:
// - 'printful' → Route to Printful API
// - 'cjdropshipping' → Route to CJ API
// - 'manual' → Notify admin for manual fulfillment
// - null → Default handling

// Order processing logic:
// 1. Customer completes checkout
// 2. OrderFulfillmentJob dispatched
// 3. Job groups order items by fulfillment_provider
// 4. Creates separate fulfillment orders per provider
// 5. Tracks fulfillment status per provider
```

---

## Automation Flows

### Order Processing Automation

```
Customer Checkout Complete
    ↓
Order Created (payment_status = 'paid')
    ↓
OrderFulfillmentJob dispatched
    ↓
Group items by fulfillment_provider
    ↓
┌─────────────────┬──────────────────┬─────────────────┐
│ Printful Items   │ CJ Items         │ Manual Items    │
│                  │                  │                  │
│ → Create Printful│ → Create CJ     │ → Email admin   │
│   order via API  │   order via API  │   notification  │
│ → Track status   │ → Track status   │ → Manual process│
│ → Webhook:       │ → Poll tracking  │                  │
│   shipped/failed │   daily          │                  │
└─────────────────┴──────────────────┴─────────────────┘
    ↓
Shipping confirmation email to customer
    ↓
14 days later: Review request email
    ↓
30 days later: Repeat purchase suggestion email
```

### Daily Automated Tasks

```php
// routes/console.php

// Check fulfillment status from CJ (no webhooks, requires polling)
Schedule::command('fulfillment:check-cj-status')->everyFourHours();

// Send abandoned cart emails
Schedule::command('cart:send-abandoned-emails')->hourly();

// Send review request emails (14 days after delivery)
Schedule::command('reviews:send-requests')->dailyAt('10:00');

// Refresh Google Shopping feed cache
Schedule::command('feeds:refresh-google-shopping')->dailyAt('06:00');

// Check low inventory alerts
Schedule::command('inventory:check-low-stock')->dailyAt('08:00');

// Send scheduled newsletter campaigns (existing)
Schedule::command('newsletters:send-scheduled')->everyFiveMinutes();

// Send appointment reminders (existing)
Schedule::command('appointments:send-reminders')->dailyAt('09:00');
```

---

## Environment Variables for New Integrations

```env
# Printful
PRINTFUL_API_KEY=your_printful_api_key
PRINTFUL_WEBHOOK_SECRET=your_webhook_secret

# CJDropshipping
CJ_API_KEY=your_cj_api_key
CJ_EMAIL=your_cj_email
CJ_PASSWORD=your_cj_password

# Google
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
GOOGLE_MERCHANT_CENTER_ID=your_merchant_id

# Meta
META_PIXEL_ID=your_pixel_id

# TaxJar (Phase 2)
TAXJAR_API_KEY=your_taxjar_api_key

# ShipBob (Phase 3)
SHIPBOB_API_KEY=your_shipbob_api_key
SHIPBOB_CHANNEL_ID=your_channel_id
```

---

## Implementation Timeline

| Week | Integration | Effort |
|------|------------|--------|
| Week 1 | Google Analytics 4 + Meta Pixel | 4-6 hours |
| Week 2 | Printful API service + webhook handler | 8-12 hours |
| Week 3 | CJDropshipping API service + order flow | 8-12 hours |
| Week 4 | Google Shopping feed generator | 4-6 hours |
| Week 5 | Email automation (welcome, abandoned cart) | 6-8 hours |
| Week 6 | Order fulfillment automation + tracking | 6-8 hours |
| Month 3-4 | TaxJar integration | 6-8 hours |
| Month 6+ | ShipBob integration (if needed) | 8-12 hours |

**Total Phase 1 Development:** ~40-50 hours

---

*All API integrations should include comprehensive error handling, logging, and retry logic. Use Laravel's queue system for external API calls to prevent checkout delays.*
