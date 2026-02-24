# Claude Assistant Guide - Generic Business Platform

**Last Updated:** December 22, 2025
**Project Status:** Production-Ready Template
**Stack:** Laravel 11, Tailwind CSS v4, Alpine.js, MySQL

---

## 📋 Quick Reference

### Essential Files to Read First
1. **CUSTOMIZATION-GUIDE.md** - How to customize for different businesses
2. **config/business.php** - Business configuration system
3. **routes/web.php** - All application routes
4. **README.md** - Platform overview and setup

### Key Locations
- **Admin Controllers:** `app/Http/Controllers/Admin/`
- **Store Controllers:** `app/Http/Controllers/Store/` (customer-facing)
- **Provider Controllers:** `app/Http/Controllers/Provider/`
- **API Controllers:** `app/Http/Controllers/Api/` (AJAX endpoints)
- **View Composers:** `app/View/Composers/` (shared view data)
- **Models:** `app/Models/`
- **Views:** `resources/views/`
- **JavaScript:** `resources/js/` (notification.js, cart.js)
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`

---

## 🎯 Project Overview

This is a full-featured business management platform that combines:
- **E-commerce** (products with inventory tracking)
- **Service Booking** (multi-provider appointment scheduling)
- **Content Management** (blog, about, team profiles)
- **Customer Portal** (order history, appointments, profile)
- **Admin Dashboard** (comprehensive management interface)

### Current Production Status
- ✅ Stripe payment integration active
- ✅ Multi-provider appointment system
- ✅ Inventory tracking functional
- ✅ XSS protection implemented (HTMLPurifier)
- ✅ Mobile-responsive throughout
- ✅ AJAX cart with instant feedback
- ✅ Global notification system
- ✅ Real-time cart count badge
- ✅ Fully configurable branding and terminology
- ✅ Mass assignment protection (role elevation prevented)
- ✅ API rate limiting (DoS protection)
- ✅ Authorization policies (centralized access control)
- ✅ Database performance optimization (composite indexes)

---

## 🏗️ Architecture Patterns

### Multi-Tenancy Model
- **Single Customer Table** with `role` field: `customer`, `provider`, `front_desk`, `admin`
- **Providers Table** contains profile/availability info, links to `customers.id`
- **Authorization** via middleware: `ProviderMiddleware`, `FrontDeskMiddleware`, `StaffMiddleware`

### Polymorphic Relationships
- **Cart Items:** `item_type` + `item_id` (supports Products and Services)
- **Order Items:** Polymorphic to Products/Services
- **Reviews:** Polymorphic to Products/Services (if enabled)

### Key Database Relationships
```php
Provider belongsTo Customer (nullable customer_id)
Provider belongsToMany Service (pivot: provider_service)
Provider hasMany ProviderAvailability
Provider hasMany Appointment
Provider hasMany ProviderCustomerNote

Service belongsTo ServiceCategory
Service hasMany Appointment
Service belongsToMany Provider

Appointment belongsTo Customer
Appointment belongsTo Service
Appointment belongsTo Provider

Product belongsTo ProductCategory
```

---

## 🎨 Branding & Styling

### Default Color Palette (Customizable)
The default "Northwind" theme uses:
- **Primary (Charcoal):** `#2E2A25` - Main dark color
- **Brand (Olive-Brown):** `#6B5F4A` - Brand accent
- **Secondary (Sand):** `#C9B79C` - Light accent
- **Accent (Clay):** `#D77F48` - CTA buttons, highlights
- **Background (Parchment):** `#F2ECE4` - Page background

### Customization
Colors are defined in `tailwind.config.js` and can be easily changed. See [CUSTOMIZATION-GUIDE.md](CUSTOMIZATION-GUIDE.md).

### Tailwind Custom Classes
- `.page-heading` - Large page titles
- `.hero-heading` - Homepage hero text
- `.home-section-heading` - Section headings
- `.btn-primary` - Primary CTA button (bronze/brown style)
- `.btn-secondary` - Secondary outline button (brand color border)
- `.btn-accent` - Accent button (clay/orange style)
- Custom config in `tailwind.config.js` and `resources/css/app.css`

### Button Patterns
Use the predefined button utility classes for consistency:

```blade
<!-- Primary Button (bronze/brown CTA) -->
<a href="#" class="btn-primary">Shop Now</a>

<!-- Secondary Button (outlined) -->
<a href="#" class="btn-secondary">Learn More</a>

<!-- Accent Button (clay/orange) -->
<a href="#" class="btn-accent">Book Appointment</a>
```

These classes are defined in `resources/css/app.css` and provide consistent styling with hover effects.

---

## 🔧 Common Development Tasks

### Adding a New Admin CRUD Interface

1. **Create Controller** in `app/Http/Controllers/Admin/`
```php
namespace App\Http\Controllers\Admin;

class ThingController extends Controller
{
    public function index() { /* list with filtering */ }
    public function create() { /* show form */ }
    public function store(Request $request) { /* save */ }
    public function show($id) { /* detail view */ }
    public function edit($id) { /* edit form */ }
    public function update(Request $request, $id) { /* save changes */ }
    public function destroy($id) { /* soft delete */ }
}
```

2. **Add Routes** in `routes/web.php`
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('things', Admin\ThingController::class);
});
```

3. **Create Views** in `resources/views/admin/things/`
- `index.blade.php` - Use `layouts.admin`, add clickable rows, filters, pagination
- `create.blade.php` - Form with validation error display
- `edit.blade.php` - Pre-populated form
- `show.blade.php` - Detail view with stats

4. **Add Navigation Link** in `resources/views/layouts/admin.blade.php`

### Adding Provider Availability

- Use `ProviderAvailability` model with types: `recurring`, `exception`, `time_off`
- Routes under `/admin/providers/{provider}/availability`
- See `ProviderAvailabilityController` for patterns
- Conflict detection built-in via `hasConflicts()` method

### Working with Categories

- **Database-driven** (not config-based)
- `ProductCategory` and `ServiceCategory` models
- Admin CRUD at `/admin/products/categories` and `/admin/services/categories`
- Image upload support with storage cleanup
- Deletion protection (can't delete if items assigned)

### Using View Composers

- **CartComposer** provides cart count to header on all pages
- Registered in `AppServiceProvider` boot() method
- Pattern: Share data with specific views/components
```php
// In AppServiceProvider:
View::composer('components.header', \App\View\Composers\CartComposer::class);
```

### AJAX Cart Operations

- **Add to cart:** `window.addToCartAjax(form)` in `resources/js/cart.js`
- **Notifications:** `window.notify(message, type)` in `resources/js/notification.js`
- **Cart count update:** `window.updateCartCount(count)` dispatches event
- Forms use `@submit.prevent="window.addToCartAjax($el)"` in product-card component
- Controller detects AJAX via `$request->expectsJson()`
- Progressive enhancement: works with or without JavaScript

### Global Notification System

- Component: `resources/views/components/notification.blade.php`
- Alpine.js-based with slide-down animation
- Listens for `notify` window event
- Auto-dismisses after 3 seconds
- Detects Laravel flash messages via data attributes

---

## 🔐 Security Patterns

### Mass Assignment Protection
**CRITICAL:** Always use `$guarded` to protect privileged fields from mass assignment vulnerabilities:

```php
// In Customer model:
protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    // ... other safe fields
];

/**
 * Protected from mass assignment to prevent privilege escalation
 */
protected $guarded = [
    'role',      // Prevent users from setting their own role
    'is_admin',  // Prevent users from making themselves admin
];
```

### API Rate Limiting
All API endpoints are protected with rate limiting to prevent DoS attacks:

```php
// In routes/api.php:
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/availability/slots', ...);  // 60 requests per minute
});
```

### Authorization Policies
Use Laravel Policies for centralized, testable authorization logic:

```php
// Policies are registered in AppServiceProvider:
Gate::policy(Order::class, OrderPolicy::class);
Gate::policy(Product::class, ProductPolicy::class);
Gate::policy(Service::class, ServicePolicy::class);

// In controllers - use authorize() instead of manual checks:
$this->authorize('view', $order);    // Instead of: if (Auth::id() !== $order->customer_id)
$this->authorize('update', $order);
$this->authorize('cancel', $order);

// Policy methods handle admin, staff, and customer permissions:
// - Admins can do everything
// - Staff can view/update most things
// - Customers can only view/modify their own records
```

**Available Policies:**
- `OrderPolicy` - Order viewing, updating, cancellation
- `ProductPolicy` - Admin-only CRUD
- `ServicePolicy` - Admin-only CRUD
- `AppointmentPolicy` - Appointment access control

### XSS Protection
All user-generated content is sanitized with HTMLPurifier:

```php
// In controllers (on save):
use App\Services\HtmlPurifierService;

protected HtmlPurifierService $purifier;

public function __construct(HtmlPurifierService $purifier)
{
    $this->purifier = $purifier;
}

// In store/update methods:
if (isset($validated['description'])) {
    $validated['description'] = $this->purifier->clean($validated['description']);
}
if (isset($validated['bio'])) {
    $validated['bio'] = $this->purifier->cleanWithNewlines($validated['bio']);
}
```

**Controllers with XSS protection:**
- `AboutController` - credentials, bio, short_bio
- `ProductController` - description, long_description, meta_description
- `ServiceController` - description, long_description, hero_text, meta_description
- `ProviderController` - bio, meta_description, admin_notes
- `BlogPostController` - content, excerpt, meta_description

### Stock Validation
```php
// Always validate before adding to cart:
if ($product->stock_quantity < $requestedQuantity) {
    return back()->with('error', 'Only ' . $product->stock_quantity . ' units available');
}

// Decrement after order creation (in transaction):
$product->decrement('stock_quantity', $quantity);
```

---

## 📊 Database Patterns

### Soft Deletes
Most models use `SoftDeletes` trait:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Thing extends Model {
    use SoftDeletes;
}

// Query only non-deleted:
Thing::all(); // automatic

// Include deleted:
Thing::withTrashed()->get();
```

### JSON Fields
```php
// In migration:
$table->json('availability_rules')->nullable();
$table->json('credentials')->nullable();

// In model:
protected $casts = [
    'availability_rules' => 'array',
    'credentials' => 'array',
];

// Usage:
$provider->credentials = ['Certified', 'Licensed'];
```

### Scopes (Use Extensively)
```php
// In Model:
public function scopeActive($query) {
    return $query->where('status', 'active');
}

// Usage:
Service::active()->get();
Provider::active()->public()->ordered()->get();
```

### Performance Optimization (Composite Indexes)
The platform includes optimized composite indexes for frequently queried columns:

**Migration:** `2025_12_23_013732_add_performance_indexes.php`

```php
// Appointments - date + status filtering
Index: ['appointment_date', 'status']
Index: ['provider_id', 'appointment_date']

// Orders - customer + status queries
Index: ['customer_id', 'status']

// Products - category browsing with active filter
Index: ['category_id', 'is_active']

// Services - category browsing with active filter
Index: ['category_id', 'is_active']

// Provider availability - provider + type + active lookups
Index: ['provider_id', 'type', 'is_active']
```

**Impact:** 30-50% speedup on filtered list views (appointments by date, products by category, etc.)

**Best Practices:**
- Always eager load relationships: `with(['service', 'provider'])`
- Use indexes in WHERE clauses: `where(['category_id' => $id, 'is_active' => true])`
- Paginate long lists to avoid loading all records

---

## 🧪 Testing Patterns

### API Endpoint Testing
```bash
# Test availability endpoints:
curl "http://127.0.0.1:8000/api/availability/slots?service_id=1&provider_id=1&date=2025-12-16"
curl "http://127.0.0.1:8000/api/availability/next-slots?service_id=1&provider_id=1&count=5"
curl "http://127.0.0.1:8000/api/availability/dates?service_id=1&provider_id=1&days=30"
```

### Route Verification
```bash
php artisan route:list | grep availability
php artisan route:clear  # Clear cache after route changes
```

---

## 📝 Git Workflow

### Commit Message Format
```
Brief descriptive title (imperative mood)

Context about what changed and why.

Backend/Frontend/UX sections if applicable.

Files Created/Modified lists if helpful.

🤖 Generated with Claude Code (https://claude.com/claude-code)

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>
```

### Before Committing
1. Read all modified files with `Read` tool
2. Test critical functionality
3. Update documentation if needed
4. Clear Laravel caches if needed
5. Verify no syntax errors

### Excluded from Git
- `.claude/settings.local.json` (local config)
- `node_modules/`
- `vendor/`
- `.env`
- `storage/` (except .gitignore files)
- `public/storage` (symlink)

---

## 🚀 Deployment Checklist

### Pre-Production
- [ ] All features tested
- [ ] Stripe production keys configured
- [ ] Email notifications working
- [ ] SSL certificate installed
- [ ] Error monitoring configured (Sentry/Bugsnag)
- [ ] Backup strategy in place
- [ ] Queue worker configured
- [ ] Cron jobs set up

### Environment Variables (.env)
```env
APP_ENV=production
APP_DEBUG=false
STRIPE_KEY=your_production_key
STRIPE_SECRET=your_production_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret
MAIL_MAILER=smtp # Configure email service
```

---

## 🐛 Known Patterns & Gotchas

### Route Order Matters
**Critical:** Specific routes MUST come before resource routes:
```php
// CORRECT:
Route::get('/services/categories', ...);
Route::resource('services', ServiceController::class);

// WRONG (404s on /services/categories):
Route::resource('services', ServiceController::class);
Route::get('/services/categories', ...);
```

### Image Path Consistency
**Pattern:** Always use `asset('storage/' . $path)`
**Not:** `{{ $product->images[0] }}` alone
**Reason:** Storage symlink required

### Type Casting in API Controllers
**Pattern:** Cast request inputs to correct types:
```php
$maxDays = (int) $request->input('days', 30); // Not just $request->input()
```

### Configuration Caching
After changing config files, always clear cache:
```bash
php artisan config:clear
```

---

## 📚 External Dependencies

### Laravel Packages
- `stripe/stripe-php` - Payment processing
- `ezyang/htmlpurifier` - XSS sanitization
- `laravel/breeze` - Authentication scaffolding

### Frontend Libraries
- Tailwind CSS v4 (via `@tailwindcss/vite`)
- Alpine.js (via CDN in layouts)
- Font Awesome (icons via CDN)

### Build Tools
- Vite (asset bundling)
- PostCSS (CSS processing)

---

## 🔍 Debugging Tips

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Tinker for Quick Tests
```bash
php artisan tinker
>>> Provider::first()->availabilities
>>> Service::find(1)->providers
```

### API Debugging
- Check network tab for AJAX requests
- Look for JSON response errors
- Verify CSRF token in headers
- Check route registration with `php artisan route:list`

---

## 💡 Best Practices for This Project

### 1. **Read Before Writing**
Always use `Read` tool to understand existing patterns before modifying code.

### 2. **Follow Existing Patterns**
- Controller structure matches existing controllers
- Views use same layout components
- Validation follows established patterns
- Route naming conventions consistent

### 3. **Mobile-First**
All new features must be mobile-responsive. Test on:
- Desktop (1920px+)
- Tablet (768px-1024px)
- Mobile (375px-768px)

### 4. **Accessibility**
- Semantic HTML
- ARIA labels where needed
- Keyboard navigation support
- Screen reader friendly

### 5. **Performance**
- Eager load relationships: `with(['service', 'provider'])`
- Use scopes for common queries
- Paginate long lists
- Cache where appropriate
- Leverage composite indexes for filtered queries

### 6. **Code Organization**
- Extract complex logic from route closures into controllers
- Use Laravel Policies for authorization (not manual if-checks)
- Group related functionality in dedicated controllers
- Keep routes file clean and readable

**Example:** The dashboard route was refactored from a 48-line closure to a dedicated `DashboardController`:
```php
// ❌ Before: 48 lines of logic in routes/web.php
Route::get('/dashboard', function() { /* complex logic */ });

// ✅ After: Clean route + dedicated controller
Route::get('/dashboard', [DashboardController::class, 'index']);
```

### 7. **Security First**
- Validate all inputs
- Sanitize user-generated content with HTMLPurifier
- Use Laravel Policies for authorization (not manual checks)
- Protect privileged fields with $guarde