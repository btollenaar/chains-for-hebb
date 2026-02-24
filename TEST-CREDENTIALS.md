# Test Credentials Reference

**Last Updated:** February 19, 2026

All test accounts use password: **`password`**

## Comprehensive Test Data Available

The platform includes POD-specific test data for realistic print-on-demand testing:

- **36 Products** with **264 variants** and **36 mockup images** distributed across **20 POD product categories** (2 levels: 4 top-level + 16 subcategories)
  - Categories: Apparel (T-Shirts, Tank Tops, Long Sleeves, Hoodies, Jackets, All-Over Print), Accessories (Hats, Bags, Phone Cases, Stickers), Home & Living (Mugs, Posters, Pillows, Blankets), Kids & Baby (Kids' Tees, Baby Onesies)
  - All products are Printful POD items with proper `fulfillment_type = 'printful'`, `printful_product_id`, `base_cost`, and `profit_margin`
  - Every product has size/color variants with `printful_variant_id`, `printful_cost`, and `retail_price`
  - Every product has a `ProductMockup` record with an Unsplash photo URL (displayed on storefront and admin)
  - Pricing range: $5.99 (stickers) - $59.99 (blankets), with realistic Printful markups (2.3x-3.1x)
  - Stock: 999 (POD = always in stock), `low_stock_threshold` = 0
  - Sample products: Classic Logo Tee, Mountain Sunset Tee, Portland Oregon Tee, Classic Logo Hoodie, Portland Crewneck Sweatshirt, Morning Brew 11oz Mug, Mountain Panorama Poster, etc.
  - Seeders: `ComprehensiveProductCategorySeeder.php`, `Comprehensive100ProductSeeder.php`
  - Fulfillment: All products fulfilled via Printful API on order payment

- **6 Test Users** (1 admin, 5 customers + 4 legacy customers)
  - Seeders: `CustomerSeeder.php` (admin + 4 legacy customers), `DummyUsersSeeder.php` (5 customers)

- **5 Default Customer Tags** — VIP, Wholesale, Brand Fan, Repeat Customer, Newsletter VIP
  - Seeder: `TagSeeder.php`

- **REST API** — Token-based authentication via Laravel Sanctum
  - Login: `POST /api/v1/auth/login` with email, password, device_name → returns bearer token
  - Profile: `GET /api/v1/auth/user` (requires Authorization header)
  - Products: `GET /api/v1/products` (public, paginated)
  - Orders: `GET /api/v1/orders` (authenticated, own orders)

**To load all test data:** Run `php artisan migrate:fresh --seed`

---

## Admin Account

| Name | Email | Role |
|------|-------|------|
| Harry Henderson | harry.admin@printstore.com | Admin (default) |

## Regular Customers

| Name | Email |
|------|-------|
| John Doe | john@example.com (original) |
| Jane Smith | jane@example.com (original) |
| Robert Johnson | robert@example.com (original) |
| Emily Davis | emily@example.com (original) |
| Lisa Patterson | lisa.customer@example.com |
| Mark Johnson | mark.customer@example.com |
| Amanda Garcia | amanda.customer@example.com |
| Chris Taylor | chris.customer@example.com |
| Jennifer Lee | jennifer.customer@example.com |

## Running the Seeders

```bash
# Run all seeders (fresh database)
php artisan migrate:fresh --seed

# Run specific seeders
php artisan db:seed --class=DummyUsersSeeder
php artisan db:seed --class=ComprehensiveProductCategorySeeder
php artisan db:seed --class=Comprehensive100ProductSeeder
php artisan db:seed --class=TagSeeder

# Run all seeders (keep existing data)
php artisan db:seed
```

## Testing Scenarios

### Role Access Testing
- **Admin**: Full access to admin panel, all management features
- **Customer**: Can browse products, place orders, view order history

### Product Browsing & Checkout Flow
1. Login as a customer (e.g., lisa.customer@example.com)
2. Browse products by category or use search
3. Add items to cart with desired quantity
4. Proceed to checkout, select shipping method
5. Complete payment via Stripe (test mode)
6. Verify order confirmation email and order detail page

### Printful Fulfillment Flow
- All 36 seeded products have `fulfillment_type = 'printful'` and are automatically sent to Printful API after payment
- Admin can view fulfillment status on order detail page
- Printful webhooks update tracking info when orders ship
- Test with Printful sandbox API key for safe testing

### Product Variant Testing
- All products have pre-configured size/color variants (264 total)
- Browse apparel products (t-shirts, hoodies, jackets) — each has 8-25 variants
- Verify size/color variant options display correctly with color swatches and size pills
- Add different variants to cart and verify they are treated as separate line items
- Check that variant-specific pricing is applied at checkout (2XL+ sizes have $3 surcharge)

## Sample Coupon Codes

The seeder creates **10 test coupons** for testing the discount code system:

| Code | Type | Value | Conditions | Status |
|------|------|-------|------------|--------|
| WELCOME10 | Percentage | 10% | 1 use per customer | Active |
| SUMMER20 | Percentage | 20% | Min order $100, max discount $50 | Active |
| FLAT50 | Fixed | $50 | Min order $200 | Active |
| SAVE15 | Percentage | 15% | None | Active |
| VIP25 | Percentage | 25% | Max discount $75, 100 total uses | Active |
| EXPIRED01 | Percentage | 30% | Expired last month | Expired |
| MAXEDOUT | Fixed | $25 | 10/10 uses exhausted | Maxed Out |
| INACTIVE | Percentage | 50% | Deactivated | Inactive |
| CART5 | Percentage | 5% | Abandoned cart recovery | Active |
| LAUNCH15 | Percentage | 15% | 1 use per customer, launch week promo | Active |

**Seeder:** `CouponSeeder.php` (10 test coupons)

### Coupon Testing Scenarios
1. **Apply valid coupon** - Use WELCOME10 at checkout for 10% discount
2. **Test minimum order** - Use FLAT50 on order under $200 (should fail)
3. **Test max discount cap** - Use SUMMER20 on $500 order (discount capped at $50)
4. **Expired coupon** - Try EXPIRED01 (should show expired error)
5. **Maxed out coupon** - Try MAXEDOUT (should show usage limit error)
6. **Per-customer limit** - Use WELCOME10 twice with same customer (second should fail)
7. **Admin management** - Visit `/admin/coupons` to create/edit/delete/toggle coupons

---

## New Features Available for Testing

### Loyalty Points (Currently Disabled)
- Feature exists but is disabled in `config/business.php`
- When enabled: earn 1 point per $1 spent, redeem 100 points = $1 discount
- Customer points history at `/loyalty`, admin adjustment on customer detail page

### Global Search
- Type in the header search bar for autocomplete results
- Press Enter or visit `/search?q=keyword` for full results page
- Searches products and blog posts

### Back-in-Stock Notifications
- Visit an out-of-stock product → "Notify Me" form appears
- Run `php artisan notifications:send-back-in-stock` to process

### Address Book
- Visit `/addresses` as authenticated customer
- Add shipping/billing addresses, set defaults
- Saved addresses appear at checkout for quick selection

### Invoice PDFs
- Complete a paid order, then visit order detail
- Click "Download Invoice" button for PDF download

### GDPR Data Export
- Visit dashboard → click "Export My Data"
- Background job generates ZIP file with all personal data
- Email sent with signed download link when ready

### Audit Log
- Admin: Visit `/admin/audit-logs` to see all admin CRUD actions
- Setting model uses automatic `Auditable` trait; other models log via `AuditLog::record()` in controllers

### CSV Import
- Admin: Visit `/admin/imports` to upload CSV files
- Download templates for proper format
- Background processing with progress tracking

### REST API
- Login: `POST /api/v1/auth/login` → `{"email":"...", "password":"...", "device_name":"test"}`
- Use returned token: `Authorization: Bearer {token}`
- Products: `GET /api/v1/products`
- Orders: `GET /api/v1/orders` (authenticated)

---

## Environment Variables for Integrations

The platform supports several third-party integrations. Add the following to your `.env` file to enable them:

```env
# Stripe (required for payments)
STRIPE_KEY=your_publishable_key
STRIPE_SECRET=your_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Google Analytics 4 (optional - e-commerce tracking)
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX

# Meta Pixel (optional - conversion tracking)
META_PIXEL_ID=123456789

# Printful (required for print-on-demand fulfillment)
PRINTFUL_API_KEY=your_printful_api_key
# Use sandbox key for testing: https://developers.printful.com/docs/#section/Authentication

# Newsletter batch settings (optional)
NEWSLETTER_BATCH_SIZE=100
NEWSLETTER_BATCH_DELAY=60
```

All integrations are conditionally loaded - they only activate when their API keys are configured.

---

## Notes

- All emails are verified (email_verified_at is set)
- All addresses are Oregon-based (Clackamas, Oregon City, Lake Oswego, Milwaukie, Tigard)
- Services are disabled in PrintStore (this is a products-only storefront)
- Featured products will appear on homepage
