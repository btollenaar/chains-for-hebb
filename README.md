# PrintStore — Print-on-Demand Merch Store

[![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A full-featured print-on-demand merchandise store built with Laravel 11, powered by the Printful API. Manage custom apparel, accessories, and home goods with automated fulfillment, product variant management (sizes, colors, designs), and a modern glassmorphism storefront with dark mode.

## Key Highlights

- **Printful-Powered Fulfillment** -- Products are synced, printed, and shipped via Printful with webhook-driven status updates
- **Variant Management** -- Sizes, colors, and design files per product with mockup generation
- **Modern Storefront** -- Glassmorphism design system, GSAP scroll animations, dark mode
- **376 Tests, 0 Failures** -- Comprehensive test suite covering checkout, payments, admin CRUD, and more

## Features

### E-Commerce & Products
- Product catalog with hierarchical categories (unlimited nesting)
- **Printful integration** -- catalog browsing, variant sync (sizes/colors), design file management, mockup generation
- **Variant management UI** -- inline price editing, bulk markup/activate/deactivate, live profit calculation
- **Enhanced search & filtering** -- color swatches, size pills, price range, in-stock toggle on shop and category pages
- Shopping cart with guest and authenticated sessions
- Seamless cart migration when guests log in or register
- Coupon/discount codes (percentage or fixed-amount, usage limits, expiration)
- Weight-based shipping calculator (free/standard/express)
- Product recommendations ("Customers Also Bought" and "Similar Products")
- Secure checkout with Stripe integration
- Order management and history with one-click reorder
- Stock level tracking and low-stock email alerts
- Customer reviews with star ratings, verified purchase badges, and admin moderation
- Google Shopping feed at `/feed/google-shopping.xml`
- Back-in-stock email notifications for out-of-stock products
- Image gallery with GLightbox (touch/swipe, keyboard navigation)
- **SEO** -- Open Graph/Twitter Card meta tags, JSON-LD structured data (Product, BlogPosting), XML sitemap, canonical URLs

### Printful Integration
- **Catalog browsing** -- Browse and import products from the Printful catalog
- **Product variants** -- Manage sizes, colors, and design placements per product
- **Design file management** -- Upload and assign print files to products
- **Mockup generation** -- Generate product mockups via the Printful API
- **Automated fulfillment** -- Orders routed to Printful after payment, with queue-based processing and retry logic
- **Webhook handling** -- `package_shipped`, `order_failed`, `order_canceled` events update order status automatically
- **Tracking updates** -- Shipping tracking numbers and carrier URLs synced from Printful

### Customer Experience
- Loyalty points program (1 point per $1, 100 points = $1 discount, stacks with coupons)
- Customer address book (multiple shipping/billing addresses, checkout auto-fill)
- Invoice/receipt PDF downloads (DomPDF)
- GDPR data export (ZIP with profile, orders, reviews, addresses, loyalty history)
- Wishlist/favorites with guest-to-auth migration and **shareable wishlist links**
- **Dedicated order tracking page** with carrier links and public tracking lookup (order number + email)
- Global search with autocomplete (products, blog posts)
- Dark mode with system preference detection and manual toggle

### Content & Marketing
- Blog system with categories, featured posts, and infinite scroll
- Newsletter campaign management (WYSIWYG editor, subscriber lists, open/click tracking, GDPR-compliant unsubscribe)
- Email capture popup with coupon incentive (WELCOME10)
- Welcome email drip sequence (3-step)
- Abandoned cart recovery emails (3-step with CART5 coupon)
- Win-back email campaign (2-step for inactive customers)
- Legal pages (Privacy Policy, Terms of Service, Return Policy, Shipping Policy)
- Analytics tracking (GA4 e-commerce events, Meta Pixel conversions)

### Admin Dashboard
- **Order analytics** -- Revenue trends, top products, customer metrics, period comparison (7d/30d/90d)
- **Dashboard enhancements** -- Date range selector, revenue-by-category chart, AOV trend, CSV export
- **Email template previews** -- Visual gallery of all transactional emails with desktop/mobile width toggle
- Product CRUD with 2-column card-based edit page, Printful variant management, and hierarchical category tree
- **Coupon management** -- Create/edit discount codes with usage stats, filters, CSV export
- **Customer management** -- Tagging/segmentation, bulk actions, CSV import/export
- **Review moderation** -- Approve/reject, admin responses, stats dashboard
- **Newsletter campaigns** -- Create, schedule, send, duplicate, test, track analytics
- **Audit log** -- Auto-logged admin actions with before/after diffs, IP tracking
- **Settings UI** -- Business info, logo/favicon upload, social links, homepage content, theme colors
- Responsive admin tables with mobile card layouts and FAB filter modals
- Return/refund request management with approval workflow
- Membership/subscription tier management (Stripe recurring)
- REST API with Sanctum token auth (`/api/v1/` -- products, orders, auth)

### Performance & Security
- Composite database indexes for optimized queries
- API rate limiting (60 req/min) with DoS protection
- Eager loading on all list views (no N+1 queries)
- Security headers middleware (X-Frame-Options, HSTS, Referrer-Policy, Permissions-Policy)
- XSS protection with HTMLPurifier on all user-generated HTML
- Mass assignment protection (`role`, `is_admin` fields guarded)
- Health check endpoint at `/health` (database, storage, cache monitoring)
- WCAG AA accessibility (focus-visible states, ARIA attributes, keyboard navigation)

## Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade templates, Alpine.js, Tailwind CSS v4, GSAP (ScrollTrigger)
- **Design System**: Glassmorphism, CSS custom properties, dark mode (class strategy)
- **Typography**: Inter (body), Space Grotesk (display), fluid `clamp()` sizing
- **Database**: MySQL 8.0+
- **Payments**: Stripe (checkout sessions, webhooks, subscription billing)
- **Fulfillment**: Printful API (catalog sync, order routing, webhook status updates)
- **Email**: Laravel Mail (SMTP/Mailgun/SES compatible)
- **Analytics**: Google Analytics 4, Meta Pixel
- **Build Tools**: Vite
- **Testing**: PHPUnit 11.5, Laravel Dusk

## Brand Colors

```
Primary:   #FF3366 (Hot Pink)
Secondary: #374151 (Dark Gray)
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Stripe account (for payments)
- Printful account (for fulfillment)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd printstore
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.template .env
   php artisan key:generate
   ```

4. **Configure database** -- edit `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=printstore
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configure Stripe** -- edit `.env`:
   ```env
   STRIPE_KEY=your_publishable_key
   STRIPE_SECRET=your_secret_key
   STRIPE_WEBHOOK_SECRET=your_webhook_secret
   ```

6. **Configure Printful** -- edit `.env`:
   ```env
   PRINTFUL_API_KEY=your_printful_api_key
   ```

7. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

8. **Build assets**
   ```bash
   npm run build
   # OR for development with hot reload:
   npm run dev
   ```

9. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

11. **Access the application**
    - Storefront: http://127.0.0.1:8000
    - Admin panel: http://127.0.0.1:8000/admin

## Testing

The platform includes a comprehensive test suite with **376 tests** (367 passing, 9 skipped) targeting 80%+ code coverage for critical business logic.

```bash
# Run all tests
php artisan test

# Run with coverage report
php artisan test --coverage --min=80

# Run in parallel (faster)
php artisan test --parallel
```

### Test Infrastructure

- **376 tests** -- 367 passing, 9 skipped, 0 failures
- **PHPUnit 11.5** for unit and feature tests
- **20 model factories** for realistic test data
- **SQLite in-memory database** for fast, isolated tests
- **GitHub Actions CI/CD** workflow included

For complete testing documentation, see **[TESTING.md](TESTING.md)**.

## Test Credentials

After running seeders, the following accounts are available:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `harry.admin@printstore.com` | `password` |
| **Customer** | `lisa.customer@example.com` | `password` |

See [TEST-CREDENTIALS.md](TEST-CREDENTIALS.md) for the full list of test accounts and data.

**Change these credentials in production!**

## Configuration

### Admin Settings UI (Recommended)
Non-technical users can manage settings at `/admin/settings`:
- **Profile**: Store name, tagline
- **Contact**: Email, phone, address
- **Social Media**: Facebook, Instagram, Twitter, LinkedIn
- **Branding**: Logo, favicon upload
- **Features**: Enable/disable modules (products, blog, reviews)
- **Homepage**: Hero section content and CTA buttons
- **Theme**: Color customization
- **Navigation**: Show/hide and customize nav links

### File-Based Configuration
Developers can configure via `config/business.php` for terminology, theme, payment methods, and advanced settings. All values can be overridden via `.env` variables.

## Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Configure production database, Stripe keys, and Printful API key
3. Configure email service (SMTP/Mailgun/SES)
4. Set up SSL certificate
5. Run `npm run build` for optimized assets
6. Set up queue worker for background jobs (Printful fulfillment, newsletter sending)
7. Configure cron job for Laravel scheduler:
   ```bash
   * * * * * cd /path/to/printstore && php artisan schedule:run >> /dev/null 2>&1
   ```

## Security

- **XSS Protection** -- HTMLPurifier on all user-generated content
- **CSRF Protection** -- Enabled on all forms
- **Mass Assignment Protection** -- `role` and `is_admin` fields guarded against manipulation
- **API Rate Limiting** -- 60 req/min on API endpoints, 120/min on tracking endpoints
- **Authorization Policies** -- Laravel Policies for orders, products, and admin access
- **Role-Based Access** -- Admin and customer roles
- **Secure Payments** -- PCI-compliant via Stripe (no card data stored)
- **Security Headers** -- X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS
- **Health Monitoring** -- `/health` endpoint for uptime checks

## Recent Changes

This project was forked from a generic Laravel 11 business management platform and transformed into a focused print-on-demand merch store across 11 phases:

1. Removed all service booking, appointment scheduling, and provider management features
2. Simplified roles to admin + customer (removed provider and front desk roles)
3. Integrated Printful API for product variant management, mockup generation, and automated fulfillment
4. Replaced multi-provider fulfillment routing with Printful-only pipeline
5. Updated storefront branding and color palette (hot pink #FF3366 primary)
6. Cleaned up test data seeders for print-on-demand product variants
7. Removed unused database tables, migrations, and seeders
8. Fixed all tests -- 330 passing, 0 failures
9. Feature expansion -- variant management UI, SEO/structured data/sitemap, enhanced filtering, order tracking, wishlist sharing, dashboard enhancements, email previews (376 tests passing)
10. Printful integration audit -- end-to-end fulfillment wiring (Stripe webhook auto-dispatch, order confirmation, expanded fulfillment_status, live shipping rates)
11. POD seed data overhaul -- replaced generic categories/products with 20 POD categories, 36 Printful products, 264 size/color variants, 36 mockup images
12. Product edit page redesign -- 2-column card layout matching Printful setup page (numbered steps, sticky sidebar, conditional Printful sections, product header with image)

## Documentation

- **[CLAUDE.md](CLAUDE.md)** -- Comprehensive technical guide (architecture, patterns, design system, MCP servers)
- **[TESTING.md](TESTING.md)** -- Testing guide (factories, mocking, CI/CD, coverage)
- **[TEST-CREDENTIALS.md](TEST-CREDENTIALS.md)** -- Test account credentials and data
- **[FUTURE-ENHANCEMENTS.md](FUTURE-ENHANCEMENTS.md)** -- Planned features and enhancement roadmap

## License

[MIT License](LICENSE)

---

**Built with Laravel 11 | Styled with Tailwind CSS v4 | Fulfilled by Printful | Powered by Stripe**
