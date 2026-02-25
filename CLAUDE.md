# Claude Technical Guide — PrintStore (Printful POD Platform)

**Last Updated:** February 19, 2026
**Platform:** Laravel 11 Print-on-Demand Store with Printful Integration
**Status:** Production-Ready (376 tests, 367 passing, 9 skipped, 0 failures)

---

## Quick Reference

### Essential Context
This is a print-on-demand e-commerce store built with Laravel 11 and integrated with the Printful API. Products are created by browsing Printful's catalog, uploading designs, generating mockups, and publishing to the storefront. Orders are fulfilled automatically via Printful when customers pay through Stripe.

The codebase was forked from a generic business management platform and transformed across 11 phases into a focused POD store. Services, appointments, and provider scheduling are disabled — this is a products-only storefront.

### Read These Files First
1. **CLAUDE.md** (this file) — Architecture, Printful integration, patterns
2. **config/business.php** — Business config (branding, features, Printful settings)
3. **README.md** — Installation, features, quick start
4. **TEST-CREDENTIALS.md** — Test accounts and sample data
5. **DEPLOYMENT-GUIDE.md** — Production deployment instructions

### Critical Locations
```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── PrintfulCatalogController.php  # Catalog browse, product setup, mockups
│   │   ├── ProductController.php          # Product CRUD
│   │   ├── ProductVariantController.php   # Variant inline editing & bulk ops
│   │   ├── OrderController.php            # Order management
│   │   ├── DashboardController.php        # Dashboard with period selector & CSV export
│   │   ├── EmailPreviewController.php     # Transactional email template previews
│   │   └── [various admin controllers]
│   ├── Store/
│   │   ├── ProductController.php          # Shop browsing with variant filters
│   │   ├── OrderController.php            # Order history & tracking
│   │   └── WishlistController.php         # Wishlist with sharing
│   ├── SitemapController.php              # XML sitemap
│   ├── PrintfulWebhookController.php      # Printful webhook handler
│   ├── StripeWebhookController.php        # Stripe payment webhooks
│   ├── FeedController.php                 # Google Shopping XML feed
│   └── LegalPageController.php            # Privacy, Terms, Return, Shipping
├── Jobs/
│   └── FulfillOrder.php                   # Order fulfillment routing (Printful/manual)
├── Models/
│   ├── Product.php                        # Extended with Printful fields
│   ├── ProductVariant.php                 # Size/color variants with Printful pricing
│   ├── ProductDesign.php                  # Design file placements
│   ├── ProductMockup.php                  # Generated mockup images
│   └── PrintfulCatalogCache.php           # Cached Printful catalog data
├── Services/
│   ├── PrintfulService.php                # Printful API wrapper (465 lines)
│   ├── OrderFactory.php                   # Order creation with price snapshots
│   ├── PaymentService.php                 # Stripe payment integration
│   ├── CheckoutService.php                # Checkout workflow orchestration
│   ├── ShippingService.php                # Printful + weight-based shipping rates
│   ├── TaxJarService.php                  # Sales tax calculation
│   ├── RecommendationService.php          # Product recommendations
│   ├── SitemapService.php                 # XML sitemap generation (cached)
│   └── GoogleShoppingFeedService.php      # Google Merchant feed
└── Mail/                                  # Email templates (welcome, cart, win-back, etc.)

config/business.php        # Centralized business config (branding, features, Printful)
routes/web.php             # All web routes
database/migrations/       # Includes 8 Printful-specific migrations
```

### Key Facts
- **App name:** PrintStore (configurable via `config('business.profile.name')`)
- **Roles:** Admin and Customer only (no providers/staff)
- **Features enabled:** Products, Blog, Reviews, Newsletter, Coupons
- **Features disabled:** Services, Appointments, Providers, Memberships, Loyalty Program
- **Fulfillment:** Printful API (automatic) or manual
- **Payments:** Stripe Checkout
- **Tests:** 376 total, 367 passing, 9 skipped, 0 failures (`php artisan test`)

---

## Printful Integration

This is the centerpiece of the application. The Printful API powers the entire product lifecycle from catalog browsing to order fulfillment.

### PrintfulService API Wrapper

**File:** `app/Services/PrintfulService.php` (465 lines)

The service wraps all Printful API v2 calls with caching, rate-limit handling, and error logging.

```php
$printful = app(PrintfulService::class);

// Catalog browsing (24h cache)
$products = $printful->getCatalogProducts();
$product = $printful->getCatalogProduct($printfulProductId);
$variants = $printful->getCatalogVariants($printfulProductId);

// Sync products
$syncProducts = $printful->getSyncProducts();
$syncProduct = $printful->getSyncProduct($syncProductId);

// Design files
$file = $printful->uploadFile($filePath, $type); // 'default' or 'preview'

// Mockup generation (async — poll for results)
$task = $printful->createMockupTask($productId, $fileUrl, $variantIds);
$result = $printful->getMockupTaskResult($taskId);
$result = $printful->generateAndWait($productId, $fileUrl, $variantIds, $timeout);

// Design file management
$fileInfo = $printful->getFile($fileId);               // Get file URL/metadata by ID

// Orders
$order = $printful->createOrder($order, $printfulItems); // Creates DRAFT order
$printful->confirmOrder($printfulOrderId);               // Confirms draft → fulfillment
$estimate = $printful->estimateOrderCost($items, $address);

// Shipping & tax
$rates = $printful->getShippingRates($address, $items); // 30min cache
$tax = $printful->calculateTax($address, $items);

// Catalog sync (refreshes local cache)
$printful->syncCatalogToCache();
```

**Configuration (.env):**
```env
PRINTFUL_API_KEY=your_api_key_here
PRINTFUL_STORE_ID=your_store_id
PRINTFUL_WEBHOOK_SECRET=your_webhook_secret
```

**Config location:** `config/business.php` → `printful` section, also `config/services.php` → `printful`

### Database Schema (Printful & Variant Migrations)

**Products table additions:**
```
printful_product_id       — Printful catalog product ID
printful_sync_product_id  — Printful sync product ID (after publishing)
printful_synced_at        — Last sync timestamp
fulfillment_type          — 'printful' or 'manual' (default: printful)
base_cost                 — Printful base cost for margin calculation
profit_margin             — Decimal profit margin percentage
```

**product_variants table:**
```
id, product_id (FK), printful_variant_id, printful_sync_variant_id,
color_name, color_hex, size, sku,
printful_cost, retail_price, is_active, stock_status,
created_at, updated_at
```

**product_designs table:**
```
id, product_id (FK), placement, file_url,
printful_file_id, width, height, dpi,
created_at, updated_at
```

**product_mockups table:**
```
id, product_id (FK), product_variant_id (nullable FK),
mockup_url, template_id, placement, is_primary, sort_order,
created_at, updated_at
```

**printful_catalog_cache table:**
```
id, printful_product_id, name, description, category,
image_url, variant_count, min_price, max_price,
colors_json, sizes_json, print_areas_json, cached_at,
created_at, updated_at
```

**order_items additions:** `variant_id`, `variant_name`, `printful_variant_id`
**cart additions:** `product_variant_id`
**customers additions:** `wishlist_share_token` (64-char unique, for shareable wishlist links)

### Product Lifecycle

```
1. Browse Catalog     → Admin browses Printful catalog (cached locally)
2. Setup Product      → Admin selects product, configures variants/pricing
3. Upload Design      → Admin uploads design files, assigns placements
4. Generate Mockups   → Printful generates product mockup images (async)
5. Publish            → Product goes live on storefront with variants
6. Customer Orders    → Customer selects variant, checks out via Stripe
7. Fulfillment        → FulfillOrder job sends order to Printful API
8. Shipping           → Printful prints, packs, ships directly to customer
9. Webhook Updates    → Printful sends status updates (shipped, failed, etc.)
```

### Admin Catalog Routes

```
GET  /admin/printful/catalog                  # Browse cached Printful catalog
GET  /admin/printful/catalog/{id}/setup       # Product setup form
POST /admin/printful/catalog                  # Create product from catalog
POST /admin/printful/catalog/sync-catalog     # Refresh catalog cache
POST /admin/printful/catalog/{id}/upload-design    # Upload design file
POST /admin/printful/catalog/{id}/generate-mockups # Generate mockups
```

**Controller:** `app/Http/Controllers/Admin/PrintfulCatalogController.php` (299 lines)

### Phase 9 Feature Routes

```
# Variant Management (admin)
PATCH /admin/products/{product}/variants/{variant}       # Inline variant update
POST  /admin/products/{product}/variants/bulk-update     # Bulk markup/activate/deactivate

# Order Tracking
GET   /orders/{order}/tracking                           # Authenticated tracking page
GET   /track                                             # Public tracking lookup form
POST  /track                                             # Public tracking lookup (order# + email)

# Wishlist Sharing
POST  /wishlist/share                                    # Generate share token (auth)
GET   /wishlist/shared/{token}                           # Public shared wishlist view

# SEO
GET   /sitemap.xml                                       # XML sitemap (cached 1h)

# Dashboard
GET   /admin/dashboard/export                            # CSV export (?period=7|30|90)

# Email Previews
GET   /admin/email-previews                              # Template gallery
GET   /admin/email-previews/{template}                   # Render preview (e.g. welcome, order-confirmation)
```

### Order Fulfillment Flow

**File:** `app/Jobs/FulfillOrder.php`

```php
// Dispatched from TWO places (idempotency guard: fulfillment_status === 'pending'):
// 1. StripeWebhookController::handleCheckoutSessionCompleted() — reliable path
// 2. CheckoutController::success() — Stripe redirect fallback
// 3. CheckoutController::processPayment() — cash/check orders
FulfillOrder::dispatch($order);

// Job groups items by fulfillment provider (default: 'printful') and routes:
match ($provider) {
    'printful' => $this->fulfillWithPrintful($order, $items),
    'manual'   => $this->notifyManualFulfillment($order, $items),
    default    => $this->notifyManualFulfillment($order, $items),
};
```

- 3 retries with 60-second backoff
- Default provider is `'printful'` (POD-exclusive store)
- Maps order items to Printful sync_variant_ids
- Creates Printful **draft** order via API, then **confirms** it for fulfillment
- Stores `fulfillment_order_id` on order record
- Marks order `fulfillment_status = 'failed'` if no valid SKUs found or on permanent failure

### Printful Webhooks

**File:** `app/Http/Controllers/PrintfulWebhookController.php`
**Route:** `POST /printful/webhook` (CSRF excluded)

**Events handled:**
| Event | Action |
|-------|--------|
| `package_shipped` | Updates tracking number, carrier, shipped_at; sends OrderStatusUpdateMail |
| `order_failed` | Sets fulfillment_status to 'failed', logs error |
| `order_canceled` | Sets fulfillment_status to 'cancelled' |
| `product_updated` | Syncs product data from Printful |
| `stock_updated` | Updates variant stock_status |

**Signature verification:** HMAC SHA256 using `PRINTFUL_WEBHOOK_SECRET`

**`fulfillment_status` lifecycle:** `pending` → `processing` → `shipped` → `delivered` → `completed`
**Error states:** `failed`, `cancelled`

### Key Models

**ProductVariant** (`app/Models/ProductVariant.php`):
```php
// Computed attributes
$variant->profit;         // retail_price - printful_cost
$variant->profit_margin;  // (profit / retail_price) * 100
$variant->is_in_stock;    // stock_status === 'in_stock'
$variant->display_name;   // "Color - Size"

// Scopes
ProductVariant::active()->inStock()->forColor('Black')->forSize('L')->get();
```

**ProductMockup** (`app/Models/ProductMockup.php`):
```php
ProductMockup::primary()->ordered()->get();
```

**PrintfulCatalogCache** (`app/Models/PrintfulCatalogCache.php`):
```php
PrintfulCatalogCache::category('T-Shirts')->search('premium')->get();
PrintfulCatalogCache::stale()->get(); // Older than 24 hours
```

---

## Architecture Overview

### Roles
- **Admin** — Full access to admin panel, product management, Printful catalog, orders
- **Customer** — Browse products, checkout, order history, reviews, wishlist

### Key Relationships
```php
Product hasMany ProductVariant
Product hasMany ProductDesign
Product hasMany ProductMockup
Product belongsTo ProductCategory (legacy category_id — nullable)
Product belongsToMany ProductCategory (pivot: product_product_category — primary)

ProductVariant belongsTo Product
ProductVariant hasMany ProductMockup

Order belongsTo Customer
Order hasMany OrderItem
OrderItem belongsTo ProductVariant (nullable)

Customer hasMany Order
Customer hasMany Cart
Customer hasMany Review
Customer hasMany Wishlist
```

### Polymorphic Patterns
```php
// Cart items and order items use polymorphic item references
cart:        item_type ('App\Models\Product'), item_id
order_items: item_type ('App\Models\Product'), item_id

// Reviews are polymorphic (products only in practice)
reviews:     reviewable_type, reviewable_id
```

### Disabled Features
The following features exist in the codebase but are disabled via `config/business.php`:
- Services, ServiceCategory, ServiceBundle
- Appointments, Provider, ProviderAvailability
- Membership tiers and subscriptions
- Loyalty points program

These models/controllers still exist but are not routed or seeded.

---

## Design System

### Brand Colors

```javascript
// tailwind.config.js
colors: {
    'earth-primary': '#FF3366',   // Hot pink — primary accent, gradients, CTAs
    'earth-green': '#374151',     // Dark gray — secondary, text
    'earth-rose': '#FF6B8A',      // Light pink — decorative accents
    'earth-success': '#10B981',   // Green — success states
    'earth-amber': '#F59E0B',     // Amber — warning states
    'earth-sage': '#6B7280',      // Gray — info, muted elements
    'earth-copper': '#FB923C',    // Orange — tertiary accents
    'admin-teal': '#2D6069',      // Teal — admin panel buttons
}
```

### CSS Custom Properties

```css
/* Light mode */
:root {
    --surface: #FAFAFA;
    --surface-raised: #FFFFFF;
    --on-surface: #0A0A0A;
    --on-surface-muted: #6B7280;
    --gradient-primary: linear-gradient(135deg, #FF3366, #E62E5C);
}

/* Dark mode (.dark on <html>) */
.dark {
    --surface: #0A0A0A;
    --surface-raised: #171717;
    --on-surface: #FAFAFA;
    --on-surface-muted: #9CA3AF;
}
```

### Button System
```blade
<!-- Customer-facing -->
<a href="..." class="btn-gradient">Shop Now</a>
<button class="btn-glass" style="color: var(--on-surface);">Learn More</button>

<!-- Admin panel -->
<button class="btn-admin-primary">Save Changes</button>
<a href="..." class="btn-admin-secondary">Cancel</a>
```

### Typography
- **Inter** (`font-sans`) — Body text
- **Space Grotesk** (`font-display`) — Headings, hero text
- Fluid typography via `clamp()` in `design-system.css`

### Key CSS Files
- `resources/css/design-system.css` — CSS custom properties, glassmorphism, dark mode, gradients
- `resources/css/app.css` — Tailwind utilities, admin styles, button classes

---

## Security Architecture

### Mass Assignment Protection
```php
// Customer model — role and is_admin are guarded
protected $guarded = ['role', 'is_admin'];
```

### XSS Protection
All user-generated HTML is sanitized via `HtmlPurifierService` before storage.

### Authorization
3 registered policies: `OrderPolicy`, `ProductPolicy`, `AddressPolicy`. Use `$this->authorize()` in controllers.

### Security Headers
Global `SecurityHeaders` middleware: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS.

### API Rate Limiting
All `/api/` routes: `throttle:60,1` (60 requests/minute/IP).

### Webhook Verification
- **Stripe:** Signature verification via Stripe SDK
- **Printful:** HMAC SHA256 signature verification

---

## Test Data System

### Seeders (DatabaseSeeder order)
```php
CustomerSeeder                      // Default admin account
DummyUsersSeeder                    // 5 test customers
SettingsSeeder                      // All default settings
ComprehensiveProductCategorySeeder  // 20 POD categories (4 top-level + 16 subcategories)
Comprehensive100ProductSeeder       // 36 POD products with ~264 variants + mockup images
BlogSeeder                          // Sample blog posts (3 categories, 3 posts)
AboutSeeder                         // About page content
OrderSeeder                         // Sample orders
SubscriberListSeeder                // Newsletter lists
CouponSeeder                        // 10 sample coupon codes
TagSeeder                           // Customer tags (VIP, Wholesale, etc.)
EnhancedTestDataSeeder              // Orders, reviews, newsletters, etc.
```

### Test Accounts
All passwords: `password`

| Role | Email |
|------|-------|
| Admin | `harry.admin@printstore.com` |
| Admin | `ben.hier@chains4hebb.com` |
| Admin | `mike.crisp@chains4hebb.com` |
| Customer | `lisa.customer@example.com` |
| Customer | `mark.customer@example.com` |

See `TEST-CREDENTIALS.md` for the full list.

### Reset Database
```bash
php artisan migrate:fresh --seed
```

---

## Common Development Tasks

### Adding a Product via Printful
1. Admin visits `/admin/printful/catalog`
2. Browses or searches Printful catalog (locally cached)
3. Clicks "Setup" on a product
4. Configures variants, pricing, margins
5. Uploads design files for each placement
6. Generates mockups via Printful API
7. Product is created with variants and published

### Working with Stripe Webhooks
```bash
stripe listen --forward-to localhost:8000/stripe/webhook
stripe trigger checkout.session.completed
```

### Printful Webhook Testing
```bash
# Printful webhooks hit POST /printful/webhook
# Use Printful dashboard to configure webhook URL
# Or test manually with curl + HMAC signature
```

### Clearing Caches
```bash
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear
npm run build
```

---

## Database Patterns

### Scopes
```php
Product::active()->featured()->get();
ProductVariant::active()->inStock()->forColor('Black')->get();
PrintfulCatalogCache::category('T-Shirts')->search('premium')->get();
```

### Eager Loading (prevent N+1)
```php
// Always eager load on list views
$products = Product::with(['variants', 'mockups', 'categories'])->paginate(15);
$orders = Order::with(['items.item', 'items.variant', 'customer'])->paginate(15);
```

### Hierarchical Categories
`HasHierarchicalCategories` trait on `ProductCategory`:
- Parent-child relationships with unlimited nesting
- `getAllDescendants()`, `getDescendantIds()`, `ancestors()`
- Circular reference prevention
- Recursive product count: `$category->active_product_count`

---

## Architecture Patterns

### Service Layer
Business logic lives in `app/Services/`, not controllers:
- `PrintfulService` — All Printful API calls
- `OrderFactory` — Order creation with price snapshots
- `PaymentService` — Stripe integration
- `CheckoutService` — Checkout workflow orchestration
- `ShippingService` — Printful API rates with weight-based fallback
- `TaxJarService` — Sales tax calculation
- `RecommendationService` — "Also bought" and "similar products"
- `SitemapService` — XML sitemap generation with caching
- `GoogleShoppingFeedService` — Google Merchant feed generation

### Form Requests
Validation extracted to `app/Http/Requests/`:
- `CheckoutRequest` — Checkout form with dynamic payment validation
- `StoreAppointmentRequest`, `UpdateAppointmentRequest` (legacy, still present)

### Trait-Based Composition
- `HasHierarchicalCategories` — Shared category tree logic
- `Auditable` — Auto-logs CREATE/UPDATE/DELETE (used on Setting model)

---

## Testing

### Running Tests
```bash
php artisan test                    # All 376 tests (367 pass, 9 skipped)
php artisan test --parallel         # Parallel execution
php artisan test --filter=Checkout  # Specific tests
```

### Key Test Areas
- Checkout flow (Stripe integration)
- Guest cart migration (session → authenticated)
- Authorization policies
- Printful webhook handling
- Coupon validation
- Newsletter tracking
- Variant management (admin inline editing, bulk ops)
- SEO (OG tags, JSON-LD, sitemap)
- Product filtering (color, size, price range)
- Order tracking (authenticated + public lookup)
- Wishlist sharing (token generation, public view)

### Factories
20 model factories available: Product, ProductCategory, ProductVariant, Order, OrderItem, Cart, Review, BlogPost, BlogCategory, Customer, Newsletter, SubscriberList, NewsletterSubscription, NewsletterSend, Setting, Coupon, Tag, Address, Membership, MembershipTier.

---

## Email Notification System

### Active Email Flows
| Email | Trigger | Schedule |
|-------|---------|----------|
| Welcome (3-step drip) | New registration | Every 30 min |
| Abandoned cart (3-step) | Cart idle 1h/24h/72h | Hourly |
| Win-back (2-step) | 60/90 days inactive | Daily noon |
| Order confirmation | Stripe payment success | Immediate |
| Order status update | Printful webhook (shipped/delivered) | Immediate |
| Review request | 7 days after delivery | Daily 10 AM |
| Low stock alert | Product below threshold | Daily 8 AM |
| Account claim | Guest checkout | Immediate |
| Post-purchase follow-up | 30 days after delivery | Daily 11 AM |

### Scheduler (routes/console.php)
```php
Schedule::command('inventory:check-low-stock')->dailyAt('08:00');
Schedule::command('appointments:send-reminders')->dailyAt('09:00');
Schedule::command('reviews:send-review-requests')->dailyAt('10:00');
Schedule::command('orders:send-post-purchase-follow-ups')->dailyAt('11:00');
Schedule::command('customers:send-win-back-emails')->dailyAt('12:00');
Schedule::command('newsletters:send-scheduled')->everyFiveMinutes();
Schedule::command('orders:process-new-orders')->everyFifteenMinutes();
Schedule::command('customers:send-welcome-emails')->everyThirtyMinutes();
Schedule::command('carts:send-abandoned-cart-emails')->hourly();
```

---

## Newsletter System

Complete campaign management with subscriber lists, HTML email campaigns, batch sending, and open/click tracking.

**Key routes:**
- Admin: `/admin/newsletters/campaigns/*`, `/admin/subscriber-lists/*`, `/admin/newsletter/*`
- Public: `/newsletter/subscribe` (AJAX), `/newsletter/unsubscribe`, `/newsletter/track/*`

**Sending:** Queue-based batch sending (100/batch, 60s delay) via `SendNewsletter` job.

**Tracking:** Unique 64-char tokens per send for open pixel and click redirect tracking.

**Compliance:** CAN-SPAM headers, one-click unsubscribe, physical address in footer.

---

## Review System

Polymorphic reviews with admin moderation, star ratings, verified purchase detection, and helpful voting.

**Customer routes:** `POST /products/{product}/reviews`, `POST /reviews/{review}/helpful`
**Admin routes:** `/admin/reviews/*` with approve/reject/respond actions

**Verified purchase:** Automatically detected from paid order history.

---

## Email Preview System

Admin gallery for previewing all 13 transactional email templates with real data.

**Controller:** `app/Http/Controllers/Admin/EmailPreviewController.php`
**Routes:** `GET /admin/email-previews` (gallery), `GET /admin/email-previews/{slug}` (render)

**Templates by category:**
| Category | Templates |
|----------|-----------|
| Onboarding | welcome, welcome-sequence, claim-account |
| Orders | order-confirmation, order-status-update, return-status |
| Recovery | abandoned-cart, abandoned-cart-sequence, win-back |
| Engagement | review-request, post-purchase-follow-up |
| Admin | low-stock-alert |
| Notifications | back-in-stock |

Renders actual Mailable classes with factory/database data. Requires seeded data for order-dependent templates.

---

## MCP Servers

8 MCP servers configured in `~/.claude.json`:

1. **GitHub** — Repository & issue management
2. **SQLite** — Direct database queries
3. **Sequential Thinking** — Step-by-step reasoning
4. **Chrome DevTools** — Browser inspection
5. **Perplexity** — AI-powered web search
6. **Context7** — Library documentation lookup
7. **Playwright** — Advanced browser automation
8. **Firecrawl** — Web scraping & crawling

**Verify:** `claude mcp list`

**Note:** SQLite MCP points to the Generic App database, not PrintStore's MySQL.

---

## Build History

| Phase | Description | Key Changes |
|-------|-------------|-------------|
| 1 | Fork from Generic App | Forked codebase, initial setup |
| 2 | Service layer refactoring | Extracted services, form requests, traits |
| 3 | Visual redesign | Glassmorphism, GSAP animations, dark mode |
| 4 | Admin panel — Printful product manager | PrintfulCatalogController, catalog browsing |
| 5 | Storefront — Customer-facing product pages | Product detail with variants, cart integration |
| 6 | Rebrand — Clean & modern color palette | #FF3366 primary, warm neutrals, updated surfaces |
| 7 | Printful webhooks & fulfillment job | FulfillOrder, PrintfulWebhookController |
| 8 | Fix all tests — 330 passing, 0 failures | Test fixes, cleanup |
| 9 | Feature expansion — 376 tests | Variant management UI, SEO (OG/JSON-LD/sitemap), enhanced search & filtering, order tracking page, wishlist sharing, dashboard enhancements (period selector, AOV, CSV export), email template previews |
| 10 | Printful integration audit | End-to-end fulfillment: Stripe webhook auto-dispatch, order confirmation (not draft), expanded fulfillment_status enum, Printful shipping rates, mockup URL fix, product setup form fix, error handling |
| 11 | POD seed data overhaul | Replaced 52 generic categories + 100 generic products (zero variants) with 20 POD categories + 36 Printful products + 264 variants + 36 mockup images (Unsplash). All products have fulfillment_type=printful, proper pricing, size/color combos. Admin controller 404 graceful handling. Admin product list shows mockup thumbnails. |
| 12 | Product edit page redesign | Restructured `/admin/products/{id}/edit` from single-column flat layout to modern 2-column card-based layout matching the Printful catalog setup page. Numbered step cards, sticky sidebar (status, categories, tags, pricing, inventory, SEO, actions), conditional Printful sections (read-only pricing summary, POD inventory badge, variants/designs/mockups outside main form). Header with product image + Printful badge. All admin-teal focus rings. Layout-only change — no functionality modified. |

---

## Business Context

**Platform type:** Print-on-demand merch store
**Fulfillment:** Printful (automatic via API)
**Branding:** Configurable via admin settings and `config/business.php`
**Default name:** PrintStore
**Default tagline:** "Custom merch, made on demand."
**Location:** Clackamas County, Oregon (Portland metro)
**State tax:** Oregon has no sales tax; TaxJar handles nexus in other states

### Production Deployment
**URL:** https://bentollenaar.dev
**Hosting:** DreamHost Shared Hosting
**App path:** `~/bentollenaar.dev/printstore/`
**PHP:** 8.4 (`/usr/local/php84/bin`)
**GitHub repo:** `btollenaar/pod-storefront`
**SSL:** Let's Encrypt (auto-renewed by DreamHost)

---

## Quick Command Reference

```bash
# Development
php artisan serve                    # Start dev server
npm run dev                          # Watch assets
php artisan migrate:fresh --seed     # Reset database

# Testing
php artisan test                     # Run all tests (367 pass, 9 skipped)
php artisan test --parallel          # Faster execution

# Caching
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# Stripe
stripe listen --forward-to localhost:8000/stripe/webhook

# Build
npm run build                        # Production assets

# Printful catalog refresh
php artisan tinker
>>> app(PrintfulService::class)->syncCatalogToCache()
```

---

## Critical Reminders

### DO NOT
- Remove `$guarded` fields from Customer model (prevents privilege escalation)
- Skip XSS sanitization on user-generated HTML
- Skip eager loading on list views (causes N+1 queries)
- Commit `.env` file to git
- Add extra Tailwind classes to buttons (use `btn-gradient`/`btn-glass` or `btn-admin-*`)
- Use `products.category_id` for category joins — use the `product_product_category` pivot table instead (legacy column is nullable and unreliable)

### ALWAYS
- Read existing code before modifying
- Use `$this->authorize()` for access control (not manual if/abort checks)
- Eager load relationships on queries
- Sanitize HTML with `HtmlPurifierService`
- Verify webhook signatures (Stripe and Printful)
- Clear caches after config changes
- Run `php artisan test` before committing
