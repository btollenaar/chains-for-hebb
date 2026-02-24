# Customization Guide

This guide explains how to customize the PrintStore platform for your specific print-on-demand merch store. The platform is built on Laravel 11 and integrates with Printful for product fulfillment.

## Table of Contents

1. [Business Branding](#business-branding)
2. [Color Scheme & Styling](#color-scheme--styling)
3. [Logo & Images](#logo--images)
4. [Contact Information](#contact-information)
5. [Feature Toggles](#feature-toggles)
6. [Sample Data Customization](#sample-data-customization)
7. [Email Templates](#email-templates)
8. [Advanced Configuration](#advanced-configuration)

---

## Business Branding

### Changing Business Name

The business name appears throughout the site and can be changed in your `.env` file:

```env
BUSINESS_NAME="Your Store Name"
BUSINESS_TAGLINE="Your store tagline or slogan"
BUSINESS_INDUSTRY="retail"
```

After changing these values, clear your config cache:
```bash
php artisan config:clear
```

---

## Color Scheme & Styling

### Customer-Facing Color Palette

The customer-facing design uses the `earth-*` color tokens defined in `tailwind.config.js`. Despite the `earth-` prefix (inherited from the upstream project), the PrintStore palette is clean and bold:

```javascript
// Modern palette (clean & bold) — customer-facing pages
colors: {
    'earth-primary': '#FF3366',  // Hot Pink — primary accent, gradients, CTAs
    'earth-green': '#374151',    // Dark Gray — secondary accent, text
    'earth-rose': '#FF6B8A',     // Light Pink — decorative accents
    'earth-success': '#10B981',  // Emerald — success states, pricing
    'earth-amber': '#F59E0B',    // Amber — warning, pending states
    'earth-sage': '#6B7280',     // Gray — info, secondary accents
    'earth-copper': '#F97316',   // Orange — tertiary accents
}
```

To customize, edit the hex values in `tailwind.config.js` and rebuild assets with `npm run build`.

### Legacy Colors (Admin Panel)

The admin panel retains the original earth-tone palette:

```javascript
colors: {
    'abs-primary': '#2E2A25',   // Main dark color
    'brand-color': '#6B5F4A',   // Brand accent
    'abs-secondary': '#C9B79C', // Secondary accent
    'accent-color': '#D77F48',  // Call-to-action color
    'abs-bg': '#F2ECE4',        // Background color
    'admin-teal': '#2D6069',    // Admin buttons, active states
},
```

### Fonts

The platform uses two font families loaded via Google Fonts in the layout:

- **Inter** (`font-sans`) - Body text, UI elements
- **Space Grotesk** (`font-display`) - Headings, hero text, display elements

Change fonts in `tailwind.config.js`:

```javascript
fontFamily: {
    sans: ['Inter', 'system-ui', 'sans-serif'],
    display: ['Space Grotesk', 'system-ui', 'sans-serif'],
},
```

And update the Google Fonts link in `resources/views/layouts/app.blade.php`:

```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
```

### Dark Mode

The platform supports automatic dark mode via CSS custom properties defined in `resources/css/design-system.css`:

- **Toggle:** Sun/moon icon button in the header
- **Persistence:** User preference saved to localStorage
- **System preference:** Falls back to `prefers-color-scheme` when no manual preference set
- **Implementation:** `dark` class on `<html>` element, CSS custom properties switch values

**To customize dark mode colors**, edit the `.dark` block in `resources/css/design-system.css`:

```css
.dark {
    --surface: #0f172a;           /* Page background */
    --surface-raised: #1e293b;    /* Card backgrounds */
    --on-surface: #f1f5f9;        /* Primary text */
    --on-surface-muted: #94a3b8;  /* Secondary text */
    --glass-bg: rgba(15, 23, 42, 0.7);
    --glass-border: rgba(148, 163, 184, 0.1);
}
```

### Button Utility Classes

**Customer-Facing Buttons (2026 Redesign):**
```css
.btn-gradient   - Gradient background with shine sweep on hover
.btn-glass      - Glassmorphism button with backdrop-blur and hover lift
```

**Usage in views:**
```blade
<a href="/shop" class="btn-gradient">Shop Now</a>
<button class="btn-glass" style="color: var(--on-surface);">Learn More</button>
```

**Legacy Buttons (still available):**
```css
.btn-primary   - Bronze accent button (used in admin-adjacent pages)
.btn-secondary - Outline button
```

**Customizing button colors:**
1. Edit the gradient/glass values in `resources/css/design-system.css`
2. Rebuild assets: `npm run build`
3. Clear browser cache to see changes

### Applying Changes

After modifying Tailwind config or CSS:
```bash
npm run build
# OR for development:
npm run dev
```

---

## Logo & Images

### Replacing the Logo

1. Replace `public/images/logo.png` with your logo (PNG format recommended)
2. Recommended size: 300x120px (transparent background)
3. Update favicon: Replace `public/favicon.ico`

### Hero Images

- Header background: `public/images/header-background.jpg`
- Homepage video background: `public/images/home-video-background-img.jpg`

### Logo Alt Text

The logo alt text automatically uses your business name from config. No manual changes needed!

---

## Contact Information

### Basic Contact Details

Edit `.env`:

```env
BUSINESS_EMAIL="contact@yourbusiness.com"
BUSINESS_PHONE="(555) 123-4567"

BUSINESS_ADDRESS_STREET="123 Main Street"
BUSINESS_ADDRESS_CITY="Your City"
BUSINESS_ADDRESS_STATE="ST"
BUSINESS_ADDRESS_ZIP="12345"
BUSINESS_ADDRESS_COUNTRY="United States"
```

### Social Media Links

Add your social media URLs to `.env`:

```env
BUSINESS_FACEBOOK_URL="https://facebook.com/yourbusiness"
BUSINESS_INSTAGRAM_URL="https://instagram.com/yourbusiness"
BUSINESS_TWITTER_URL="https://twitter.com/yourbusiness"
BUSINESS_LINKEDIN_URL="https://linkedin.com/company/yourbusiness"
```

Leave blank to hide social icons.

---

## Feature Toggles

Enable or disable major features in `config/business.php` or via `.env`:

```env
# Core Features
FEATURE_PRODUCTS=true        # E-commerce / product catalog (always on for PrintStore)
FEATURE_BLOG=true           # Blog/news section
FEATURE_REVIEWS=true        # Customer reviews
```

**Note:** Services and appointments are disabled in this fork (`false` in `config/business.php`). The PrintStore is focused on print-on-demand product sales, not service bookings. Other optional features (memberships, gift cards, loyalty program, multi-location) are also disabled by default.

When a feature is disabled:
- Navigation links are automatically hidden
- Related pages return 404
- Admin sections are hidden

---

## Sample Data Customization

The platform includes comprehensive seeders that create sample data for testing and development. There are two types of seeders:

### Core Seeders (Production-Ready)
- `CustomerSeeder.php` - Creates default admin account
- `AboutSeeder.php` - Creates about page content
- `ComprehensiveProductCategorySeeder.php` - Creates product categories
- `BlogSeeder.php` - Creates sample blog posts
- `SettingsSeeder.php` - Default site settings
- `SubscriberListSeeder.php` - Newsletter subscriber lists
- `TagSeeder.php` - Customer tags
- `CouponSeeder.php` - Sample discount codes

### Test Data Seeders (Development Only)
- `DummyUsersSeeder.php` - Creates test users (admins and customers)
- `Comprehensive100ProductSeeder.php` - Creates sample products across categories
- `EnhancedTestDataSeeder.php` - Orders, reviews, newsletter subscriptions, etc.
- `OrderSeeder.php` - Sample order data

**Note:** Products are managed via the Printful integration. The seeder data provides placeholder products for development and testing. In production, products are synced from your Printful catalog.

**To use test data:**
```bash
# Seed everything including test data
php artisan migrate:fresh --seed

# Seed only specific data (preserves existing data)
php artisan db:seed --class=DummyUsersSeeder
php artisan db:seed --class=Comprehensive100ProductSeeder
```

See [TEST-CREDENTIALS.md](TEST-CREDENTIALS.md) for complete list of test accounts and testing scenarios.

### 1. Admin Account

Edit `database/seeders/CustomerSeeder.php`:

```php
Customer::create([
    'name' => 'Your Admin Name',
    'email' => 'youradmin@yourbusiness.com',
    'password' => bcrypt('your-secure-password'),
    'role' => 'admin',
    'is_admin' => true,
]);
```

### 2. About Page

Edit `database/seeders/AboutSeeder.php`:

```php
About::create([
    'name' => 'Your Business or Owner Name',
    'credentials' => 'Your credentials',
    'bio' => 'Your story...',
]);
```

### 3. Product Categories

Edit `database/seeders/ComprehensiveProductCategorySeeder.php` to add your product types (e.g., T-Shirts, Hoodies, Mugs, Posters, Phone Cases).

After editing seeders:
```bash
php artisan migrate:fresh --seed
```

---

## Email Templates

Email templates are located in `resources/views/emails/`:

- `order-confirmation.blade.php` - Sent after order placed
- `order-status-update.blade.php` - Shipped/delivered notifications
- `claim-account.blade.php` - Account claim instructions for guest checkouts
- `welcome-step1/2/3.blade.php` - Welcome drip sequence (3 steps)
- `abandoned-cart-step1/2/3.blade.php` - Abandoned cart recovery (3 steps)
- `review-request.blade.php` - Review request after delivery
- `back-in-stock.blade.php` - Back-in-stock notifications
- `low-stock-alert.blade.php` - Admin low-stock alerts

### Customizing Email Content

Each template uses your business config automatically:
- Business name: `{{ config('business.profile.name') }}`
- Contact email: `{{ config('business.contact.email') }}`
- Phone: `{{ config('business.contact.phone') }}`

### Email Service Setup

Configure your email service in `.env`:

**For SMTP (Gmail, Outlook, etc.):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourbusiness.com
MAIL_FROM_NAME="${BUSINESS_NAME}"
```

**For Mailgun:**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your-mailgun-key
```

Test email sending:
```bash
php artisan tinker
>>> Mail::raw('Test email', fn($msg) => $msg->to('your-email@example.com')->subject('Test'));
```

---

## Security Considerations

**IMPORTANT:** The platform includes several security features that should NOT be disabled:

### Protected Features
- **Mass Assignment Protection** - The `Customer` model protects `role` and `is_admin` fields with `$guarded` to prevent privilege escalation
- **API Rate Limiting** - Availability endpoints are rate-limited to 60 requests/minute to prevent DoS attacks
- **XSS Sanitization** - All user content is sanitized with HTMLPurifier before saving
- **Authorization Policies** - Access control via Laravel Policies (don't bypass with manual checks)

### When Customizing
- Never remove `$guarded` fields from models
- Don't remove rate limiting from API endpoints
- Always sanitize user-generated HTML content
- Use `$this->authorize()` for permission checks (don't write custom authorization logic)
- Keep CSRF protection enabled on all forms
- Verify Stripe webhook signatures

### Adding New Fields
When adding new user-editable fields that accept HTML:

```php
// In your controller:
use App\Services\HtmlPurifierService;

protected HtmlPurifierService $purifier;

public function __construct(HtmlPurifierService $purifier)
{
    $this->purifier = $purifier;
}

// In store/update method:
if (isset($validated['your_html_field'])) {
    $validated['your_html_field'] = $this->purifier->clean($validated['your_html_field']);
}
```

---

## Advanced Configuration

### Payment Configuration

**Stripe Settings:**
```env
STRIPE_KEY=pk_live_your_key        # Publishable key
STRIPE_SECRET=sk_live_your_key     # Secret key
STRIPE_WEBHOOK_SECRET=whsec_your_secret
CASHIER_CURRENCY=usd
```

**Tax Rate:**
Edit `config/business.php`:
```php
'payments' => [
    'tax_rate' => 0.07, // 7% sales tax
],
```

### Inventory Settings

Edit `config/business.php`:
```php
'products' => [
    'track_inventory' => true,
    'allow_backorders' => false,
    'low_stock_threshold' => 10,
],
```

---

## Cache Management

After making configuration changes:

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild config cache (production only)
php artisan config:cache
```

---

## Troubleshooting

### Changes Not Appearing?

1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
2. Clear Laravel caches (see above)
3. Rebuild assets: `npm run build`
4. Check `.env` values are correct

### Logo Not Displaying?

1. Ensure `php artisan storage:link` has been run
2. Check file exists at `public/images/logo.png`
3. Verify file permissions (644)

### Emails Not Sending?

1. Check mail configuration in `.env`
2. Test with `php artisan tinker` (see Email Service Setup)
3. Check `storage/logs/laravel.log` for errors
4. Verify firewall allows outbound SMTP (port 587/465)

---

## Hierarchical Categories

### Overview

The platform supports unlimited category nesting for products. Categories can have parent-child relationships and products can belong to multiple categories with a primary designation.

### Managing Categories

**Via Admin Interface:**
1. Navigate to **Admin > Products > Categories**
2. Click "New Category" to create a category
3. Select parent category from dropdown (or leave blank for top-level)
4. The system prevents circular references automatically

**Via Code:**
```php
// Create a top-level category
$parent = ProductCategory::create([
    'name' => 'Apparel',
    'slug' => 'apparel',
    'parent_id' => null,
]);

// Create child category
$child = ProductCategory::create([
    'name' => 'T-Shirts',
    'slug' => 't-shirts',
    'parent_id' => $parent->id,
]);
```

### Assigning Products to Categories

**Multiple category assignment with primary designation:**
```php
// Assign product to multiple categories
$product->categories()->attach([
    $category1->id => ['is_primary' => true, 'display_order' => 1],
    $category2->id => ['is_primary' => false, 'display_order' => 2],
]);
```

**In admin forms:** Use the collapsible tree checkbox UI component that automatically handles hierarchy display.

### Customizing Navigation

Category navigation is automatically generated and cached. To customize:
- Desktop: Edit `resources/views/components/category-menu-item.blade.php`
- Mobile: Edit `resources/views/components/mobile-category-menu-item.blade.php`

Cache is automatically invalidated when categories or items change status.

---

## Settings Management UI

### Database-Backed Settings

All business settings are stored in the database and manageable via the admin interface at **Admin > Settings**.

### Image Upload

**Supported image types:**
- Logo: JPEG, PNG, JPG, GIF, WebP (5MB max)
- Favicon: ICO, PNG (1MB max)

**To customize branding:**
1. Go to **Admin > Settings > Branding** tab
2. Use the image upload component to upload logo/favicon
3. Metadata (dimensions, size, MIME type) is automatically extracted
4. Old files are automatically deleted when replaced

**Programmatic access:**
```php
$logo = Setting::get('logo'); // Returns full path
$logoMetadata = Setting::find('logo')->metadata; // Array with dimensions, size, etc.
```

### Theme Color Customization

**Via Admin Interface:**
1. Navigate to **Admin > Settings > Theme** tab
2. Use the interactive color picker for each theme color
3. See live preview samples
4. Click "Save Theme Settings"
5. **Important:** Refresh the page to see changes applied

**Available theme colors (admin panel):**
- Primary (charcoal #2E2A25) - Main text, headers
- Secondary (sand #C9B79C) - Backgrounds, accents
- Accent (clay #D77F48) - CTA buttons, highlights
- Admin (teal #2D6069) - Admin buttons, active states
- Background (parchment #F2ECE4) - Page background

**Customer-facing design** uses CSS custom properties from `resources/css/design-system.css` for surfaces, glass effects, and dark mode. The `earth-*` color palette (hot pink, dark gray, etc.) is defined in `tailwind.config.js`.

**Colors are injected as CSS custom properties** and applied to all button classes and color utilities automatically.

### Programmatic Setting Management

```php
use App\Models\Setting;

// Get setting value
$value = Setting::get('business_name', 'Default Name');

// Update setting
Setting::set('business_name', 'New Name');

// Set image with metadata
$setting = Setting::where('key', 'logo')->first();
$setting->setImage($request->file('logo'));
```

---

## Admin Button Styling

### Standardized Button Classes

**IMPORTANT:** Always use these standardized classes for admin buttons. Do NOT add extra Tailwind classes or inline styles.

**Primary Actions:**
```blade
<button class="btn-admin-primary">Save Changes</button>
```

**Secondary/Cancel Actions:**
```blade
<a href="..." class="btn-admin-secondary">Cancel</a>
```

**Success Actions:**
```blade
<button class="btn-admin-success">Approve</button>
```

**Small Variant:**
```blade
<button class="btn-admin-primary btn-admin-sm">Quick Action</button>
```

**Link Styles (for icon-only or inline actions):**
```blade
<a href="..." class="link-admin-info"><i class="fas fa-edit"></i> Edit</a>
<a href="..." class="link-admin-danger"><i class="fas fa-trash"></i> Delete</a>
```

### Customizing Admin Theme

Edit `resources/css/app.css` to change admin button colors:

```css
.btn-admin-primary {
    background-color: #2D6069; /* Change admin teal */
    color: white;
}
```

**Admin color palette:**
- Admin Teal: `#2D6069` - Primary admin actions
- Red: For delete/danger actions
- Green: For approve/success actions
- Gray: For secondary/cancel actions

---

## Newsletter System

### Subscriber List Management

**Create custom lists:**
1. Go to **Admin > Newsletter > Lists**
2. Click "New List"
3. Name your list (e.g., "VIP Customers", "New Subscribers")
4. System lists (All Subscribers, Customers) are protected

**Import subscribers programmatically:**
```php
use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;

$list = SubscriberList::where('slug', 'vip-customers')->first();

NewsletterSubscription::create([
    'email' => 'customer@example.com',
    'name' => 'Customer Name',
    'status' => 'active',
])->lists()->attach($list->id);
```

### Creating Campaigns

1. Navigate to **Admin > Newsletter > Campaigns**
2. Click "New Campaign"
3. Use TinyMCE WYSIWYG editor for HTML content
4. Select target lists
5. Send test email to verify
6. Schedule or send immediately

**Campaign sending:**
- Batch processing: 100 emails per batch
- Rate limiting: 60-second delay between batches
- Scheduled dispatch: Every 5 minutes via Laravel scheduler

### Email Analytics

**Available metrics:**
- Open tracking: 1x1 pixel tracking
- Click tracking: URL redirect tracking
- Open rate: Percentage of recipients who opened
- Click rate: Percentage who clicked links

Access via campaign detail page in admin.

### Unsubscribe Compliance

One-click unsubscribe links are automatically added to all campaigns. Unsubscribe page requires no login and is GDPR/CAN-SPAM compliant.

---

## Responsive Admin Patterns

### Mobile-First Design

The admin interface uses a hybrid responsive design:
- **Mobile (<768px):** Card-based layout with FAB filter button
- **Tablet (768-1024px):** Simplified table with non-critical columns hidden
- **Desktop (1024px+):** Full table with all columns

### Implementing Responsive Admin Pages

**Pattern to follow:**

```blade
<!-- Desktop Filter (Hidden on Mobile) -->
<div class="hidden md:block bg-white shadow-sm rounded-lg mb-6">
    <form method="GET" action="...">
        <!-- Filter fields -->
    </form>
</div>

<!-- Mobile Filter Modal -->
<x-admin.mobile-filter-modal formAction="{{ route('admin.items.index') }}">
    <!-- Same filter fields -->
</x-admin.mobile-filter-modal>

<!-- Mobile Cards (Visible only on mobile) -->
<div class="grid grid-cols-1 gap-4 md:hidden mb-6">
    @foreach($items as $item)
        <x-admin.table-card
            :item="$item"
            route="admin.items.edit"
            :fields="[...]"
            :actions="[...]"
        />
    @endforeach
</div>

<!-- Desktop Table (Hidden on mobile) -->
<div class="hidden md:block overflow-x-auto">
    <table>...</table>
</div>
```

### Using Table Card Component

The `table-card` component accepts:
- `item` - The model instance
- `route` - Base route name for actions
- `fields` - Array of field definitions with labels and render functions
- `actions` - Array of action buttons (route, icon, color, label)

**Example:**
```blade
<x-admin.table-card
    :item="$order"
    route="admin.orders.show"
    :fields="[
        [
            'label' => 'Customer',
            'render' => function($item) {
                return '<div class=\'font-medium\'>' . e($item->customer->name) . '</div>';
            }
        ]
    ]"
    :actions="[
        ['route' => 'admin.orders.show', 'icon' => 'fa-eye', 'color' => 'blue', 'label' => 'View']
    ]"
/>
```

### Accessibility Requirements

All admin pages must include:
- ARIA labels on icon-only buttons
- Keyboard navigation support
- WCAG AA contrast ratios
- Screen reader-friendly table structures

---

## Testing Custom Features

After customizing the platform for your business, it's important to write tests for your custom features to ensure they work correctly and don't break during future updates.

### Testing Custom Business Logic

If you add custom business logic (e.g., custom pricing calculations, Printful product sync rules), create corresponding tests:

```php
// tests/Feature/CustomPricingTest.php
public function test_vip_customers_receive_discount()
{
    $vipCustomer = Customer::factory()->create(['tier' => 'vip']);
    $product = Product::factory()->create(['price' => 100.00]);

    $order = Order::factory()
        ->for($vipCustomer)
        ->has(OrderItem::factory()->forProduct($product))
        ->create();

    // Assert VIP discount applied
    $this->assertEquals(85.00, $order->total_amount); // 15% off
}
```

### Testing Feature Toggle Configurations

Test that disabling features in `.env` actually hides them:

```php
public function test_disabled_blog_feature_not_accessible()
{
    config(['business.features.blog' => false]);

    $this->get('/blog')
        ->assertNotFound();
}
```

### Using Test Factories for Custom Models

If you create new models, always create corresponding factories:

```bash
php artisan make:factory MyCustomModelFactory
```

See [TESTING.md](TESTING.md) for detailed factory patterns and examples.

### Running Tests After Customization

Before deploying your customized version:

```bash
# Run full test suite
php artisan test --coverage

# Check for broken features
php artisan test --filter=custom

# Browser tests for UI changes
php artisan dusk
```

**Important:** Always maintain the 80%+ code coverage target when adding custom features.

---

## Quick Customization Checklist

- [ ] Set business name and tagline in `.env`
- [ ] Update contact information in `.env`
- [ ] Replace logo in `public/images/logo.png`
- [ ] Replace favicon in `public/favicon.ico`
- [ ] Customize color scheme in `tailwind.config.js`
- [ ] Add social media URLs to `.env`
- [ ] Customize seeders with your sample data
- [ ] Configure email service in `.env`
- [ ] Set up Stripe payment keys
- [ ] Run `npm run build`
- [ ] Clear caches
- [ ] Test the site!

---

**Need Help?** Check the detailed documentation in `CLAUDE.md` for technical details and troubleshooting.
