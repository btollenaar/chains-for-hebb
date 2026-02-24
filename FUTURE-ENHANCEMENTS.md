# Future Enhancements

This document tracks potential features and improvements for future implementation. Each enhancement includes a brief overview, estimated effort, and priority level.

> **Note:** This project has been forked from a general-purpose business management platform to a **print-on-demand merch store (PrintStore)** powered by Printful. Some enhancements listed below were designed for the original platform and may not be applicable to PrintStore. Service-related features (appointments, scheduling, service bundles) are disabled in this fork. CJ Dropshipping has been removed from the codebase.

**Last Updated:** February 18, 2026
**Plan Files:** See `~/.claude/plans/INDEX.md` for implementation plan mappings

---

## High Priority Enhancements

### 1. Membership Management System

**Status:** ✅ Complete (February 2026 - Phase 8.3)
**Effort:** 3-4 days for MVP, 5-7 days for full feature set
**Business Impact:** High - Recurring revenue stream, increased customer lifetime value

#### Overview
~~A comprehensive multi-tier membership system~~ **IMPLEMENTED:** Multi-tier membership/subscription system with Stripe recurring billing.

#### Implemented Features
- ✅ **Multiple membership tiers** - `membership_tiers` + `memberships` tables
- ✅ **Stripe subscription integration** - Checkout, activation, cancellation via `MembershipService`
- ✅ **Automatic discounts** - Member discounts stack after coupon discounts in OrderFactory
- ✅ **Self-service portal** - Customer pricing page + member management portal
- ✅ **Admin management** - Full CRUD for tiers + member management with CSV export
- ✅ **Stripe webhooks** - Subscription lifecycle handlers (created, updated, deleted, payment succeeded/failed)
- ✅ **Test data** - Silver/Gold/Platinum tiers seeded

#### Still Needed
- Priority booking access (members book further in advance)
- Member-exclusive content (restricted services/blog posts)
- Email automation (welcome, renewal reminders, payment notifications)
- Trial period support

**Detailed Plan:** See `~/.claude/plans/archive/polished-sauteeing-wombat.md`

---

### 2. Email Notification System

**Status:** ✅ Complete (December 2025)
**Effort:** 2-3 days
**Business Impact:** High - Critical for customer communication

#### Overview
~~Implement~~ **IMPLEMENTED:** Comprehensive email notifications for orders, appointments, and system events.

#### Implemented Features
- ✅ **Order Confirmations** - Detailed receipt with items and totals (sent after payment)
- ✅ **Appointment Confirmations** - Booking details sent immediately on booking
- ✅ **Appointment Reminders** - 24 hours before scheduled time (Laravel scheduler)
- ✅ **Account Claim Emails** - For guest customers after checkout
- ✅ **Newsletter Campaign System** - See enhancement #12 below

#### Additionally Implemented (February 2026)
- ✅ **Abandoned Cart Recovery Emails** - 3-step drip sequence (1h reminder, 24h social proof, 72h with CART5 5% discount)
- ✅ **Welcome Email Drip Sequence** - 3-step series (immediate WELCOME10 coupon, day 3 brand story, day 7 product recommendations)
- ✅ **Email Capture Popup** - Glassmorphism popup with WELCOME10 incentive, localStorage dismissal, AJAX subscribe
- ✅ **Post-Purchase Follow-Up Emails** - Sent 30 days after delivery (daily check)
- ✅ **Review Request Emails** - Sent 7 days after delivery (Phase 6.3)
- ✅ **Low-Stock Alert Emails** - Admin notification when inventory below threshold (Phase 6.2)
- ✅ **Order Status Update Emails** - Shipped/delivered notifications with tracking info (Phase 7.3)
- ✅ **Admin Notification System** - In-app bell with unread count (Phase 8.1)
- ✅ **Return Status Emails** - Return request status updates (Phase 8.2)
- ✅ **Win-Back Email Campaign** - 2-step re-engagement (60 days gentle reminder, 90 days 10% off with WELCOME10, daily scheduler 12:00 PM)

---

### 3. Guest Cart Migration on Login

**Status:** ✅ Complete (Phase 25, December 2025)
**Effort:** 2-3 hours
**Business Impact:** Medium - Improves UX, reduces cart abandonment

#### Overview
~~Automatically migrate~~ **IMPLEMENTED:** Guest cart items automatically migrate when user logs in or registers.

#### Implemented Features
- ✅ Event listeners on `Login` and `Registered` events
- ✅ Guest session ID preserved before authentication (critical fix)
- ✅ Smart quantity merging for duplicate items
- ✅ Transaction-based migration with rollback on error
- ✅ Comprehensive error logging
- ✅ Zero friction guest-to-customer conversion

---

## Medium Priority Enhancements

### 4. Review & Rating System

**Status:** ✅ Complete (Phase 24, December 2025)
**Effort:** 1-2 days
**Business Impact:** Medium - Social proof, customer feedback

#### Overview
~~Allow~~ **IMPLEMENTED:** Customers can review and rate products and services they've purchased.

#### Implemented Features
- ✅ Star ratings (1-5) with title and written reviews
- ✅ Display average rating with distribution bars on product/service pages
- ✅ Review moderation with admin approval workflow
- ✅ Verified purchase badge (automatic detection)
- ✅ Admin response system for public engagement
- ✅ Helpful/not helpful voting
- ✅ Advanced filtering (status, rating, type, verified purchase, search)
- ✅ Related reviews context
- ✅ Duplicate prevention (one review per item per customer)

#### Still Needed
- **Photos/images with reviews** - Allow customers to upload images
- **Enhanced sorting** - Sort by rating, date, helpfulness (basic sorting exists)

---

### 5. Inventory Low-Stock Alerts

**Status:** ✅ Complete (February 2026 - Phase 6.2)
**Effort:** 4-6 hours
**Business Impact:** Medium - Prevents stockouts

#### Overview
~~Notify admins~~ **IMPLEMENTED:** Admin email alerts when product inventory drops below threshold.

#### Implemented Features
- ✅ `LowStockNotification` email notification
- ✅ Configurable threshold (default 10 units, via `config('business.inventory.low_stock_threshold')`)
- ✅ `CheckLowStock` artisan command with daily scheduler
- ✅ Admin notifications via `AdminNotificationService`

#### Still Needed
- Dashboard widget showing low-stock products
- Automatic "Low Stock" badge on product pages
- Option to hide out-of-stock products

---

### 6. Loyalty Points Program

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 days
**Business Impact:** Medium - Increases repeat purchases

#### Overview
~~Customers earn points on purchases~~ **IMPLEMENTED:** Full loyalty points program with earning, redemption, and transaction history.

#### Implemented Features
- ✅ `loyalty_points` table (transaction log with positive/negative entries)
- ✅ `LoyaltyService` with `earnPoints()`, `redeemPoints()`, `calculatePointsForOrder()`, `calculateDiscountFromPoints()`
- ✅ Earn 1 point per $1 spent (configurable via Settings)
- ✅ Redeem 100 points = $1 discount (configurable)
- ✅ Points redemption at checkout (stacks with coupon discounts)
- ✅ Points history/transaction log page for customers
- ✅ Admin manual points adjustment on customer detail page
- ✅ `loyalty_points_balance` cached on customers table
- ✅ Dashboard loyalty points card
- ✅ 7 tests

#### Still Needed
- Points expiration (optional)
- Bonus points for referrals, reviews, etc.
- Integration with membership tiers (members earn more points)

---

### 7. Advanced Search & Filtering

**Status:** ✅ Complete (February 2026)
**Effort:** Complete
**Business Impact:** Medium - Improves product/service discovery

#### Overview
~~Enhanced search with faceted filtering and sorting options~~ **IMPLEMENTED:** Global search with autocomplete, full results page, and variant-level filtering.

#### Implemented Features
- ✅ Sort by name, price (asc/desc), newest (products + services, Feb 2026)
- ✅ Sort state preserved across pagination via `appends(request()->query())`
- ✅ Results count display (e.g., "42 products")
- ✅ `SearchService` with `search()` and `fullSearch()` methods across products, services, blog posts
- ✅ Header search with debounced AJAX autocomplete (4 results per category)
- ✅ Full results page with tabs (All/Products/Services/Blog)
- ✅ Mobile search support in header and mobile nav drawer
- ✅ **Color filter** — swatch-based checkboxes with hex backgrounds on shop & category pages
- ✅ **Size filter** — pill-button toggles (XS–5XL) on shop & category pages
- ✅ **Price range filter** — min/max number inputs with variant-level price filtering
- ✅ **In-stock toggle** — filter to only show products with in-stock variants
- ✅ Collapsible filter panel (Alpine.js) with active filter indicator
- ✅ Weighted search relevance (exact name match > partial name > SKU > description)
- ✅ Variant color/size searching via `whereHas`
- ✅ 15 tests (5 search + 10 product filter)

#### Still Needed
- Save search preferences

---

### 8. Multi-Location Support

> **Note:** This feature was designed for service-based businesses with physical locations. Not applicable to PrintStore.

**Status:** Not Started
**Effort:** 3-4 days
**Business Impact:** Low-Medium - Enables expansion (N/A for PrintStore)

#### Overview
Support for multiple business locations with separate inventories and staff.

#### Core Features
- Location management (address, hours, contact)
- Location-specific inventory tracking
- Providers assigned to specific locations
- Services available at specific locations
- Customers choose location when booking
- Location-based availability

---

## Low Priority Enhancements

### 9. Gift Cards & Vouchers

**Status:** Not Started
**Effort:** 2-3 days
**Business Impact:** Low-Medium - Additional revenue stream

#### Overview
Sell and redeem gift cards for products and services.

#### Core Features
- Purchase gift cards in various amounts
- Email delivery with unique code
- Redeem at checkout
- Check balance
- Expiration date management
- Admin tracking and reporting

---

### 10. Automated Appointment Reminders (SMS)

> **Note:** Appointments/scheduling are disabled in PrintStore. This feature is not applicable to the print-on-demand store.

**Status:** Email reminders needed first
**Effort:** 1 day (after email system)
**Business Impact:** Low-Medium - Reduces no-shows (N/A for PrintStore)

#### Overview
Send SMS reminders in addition to email.

#### Core Features
- Twilio integration
- Send 24-48 hours before appointment
- Confirmation reply option ("Reply Y to confirm")
- Configurable message templates
- Opt-out management

---

### 11. Customer Portal Enhancements

**Status:** ✅ Mostly Complete (February 2026)
**Effort:** 0.5-1 day remaining
**Business Impact:** Low - Improved customer self-service

#### Overview
~~Enhanced customer dashboard with more features.~~ **MOSTLY IMPLEMENTED:** Invoice downloads, dedicated tracking page, and address book.

#### Implemented Features
- ✅ Downloadable invoices/receipts (DomPDF)
- ✅ **Dedicated order tracking page** — full-page focused tracking with large timeline, carrier icon, "Track on [Carrier]" CTA
- ✅ **Public tracking lookup** — `GET /track` form (order number + email) for guest customers
- ✅ Customer address book with checkout auto-fill

#### Still Needed
- Saved payment methods
- Communication preferences
- Account activity log

---

### 12. Newsletter System

**Status:** ✅ Complete (December 25, 2025)
**Effort:** 1-2 days
**Business Impact:** Low - Marketing channel

#### Overview
~~Send newsletters~~ **IMPLEMENTED:** Complete newsletter campaign management system with rich HTML editor, subscriber lists, and analytics.

#### Implemented Features
- ✅ **Multi-list subscriber management** - System lists (all_subscribers, new_customers) + unlimited custom lists
- ✅ **Campaign CRUD** - Create, schedule, send, cancel, duplicate campaigns
- ✅ **Rich HTML email editor** - TinyMCE WYSIWYG with compliance footer
- ✅ **Queue-based batch sending** - 100 emails per batch with 60-second delay (configurable)
- ✅ **Scheduled campaigns** - Laravel scheduler dispatches every 5 minutes
- ✅ **Open/click tracking** - 1x1 pixel for opens, URL redirects for clicks
- ✅ **Analytics dashboard** - Sent/opened/clicked counts and rates per campaign
- ✅ **One-click unsubscribe** - Token-based, no login required (GDPR/CAN-SPAM compliant)
- ✅ **Test email preview** - Send test before launching campaign
- ✅ **Compliance headers** - Precedence: bulk, List-Unsubscribe, X-Auto-Response-Suppress
- ✅ **Subscriber segmentation** - Assign subscribers to custom lists
- ✅ **Send history** - Individual send records with status tracking
- ✅ **Error handling** - Per-email try-catch with comprehensive logging

#### Technical Implementation
- 5 database tables (newsletters, newsletter_sends, newsletter_subscriptions, subscriber_lists, pivot tables)
- NewsletterMail class with compliance headers
- SendNewsletter queue job with retry logic
- SendScheduledNewsletters artisan command
- Public unsubscribe views (confirmation, success, error, already unsubscribed)
- Tracking controllers (open/click)
- Admin CRUD for campaigns, subscribers, and lists

**Note:** Self-hosted solution. No third-party integration needed (Mailchimp, etc.)

---

### 13. Blog Enhancements

**Status:** Basic blog exists
**Effort:** 1-2 days
**Business Impact:** Low - SEO and engagement

#### Core Features
- Tags (in addition to categories)
- Related posts
- Author profiles (for multi-author blogs)
- Comment system (or Disqus integration)
- Social sharing buttons
- Reading time estimate
- Table of contents for long posts

---

### 14. Product Variants

**Status:** ✅ Complete (February 2026)
**Effort:** Complete
**Business Impact:** Low - Depends on product catalog

#### Overview
~~Support for product variations (size, color, etc.) with separate SKUs and inventory.~~ **IMPLEMENTED:** Full variant system with Printful integration and admin management UI.

#### Implemented Features
- ✅ Variant attributes (size, color with hex values, SKU)
- ✅ Separate pricing per variant (printful_cost, retail_price)
- ✅ Active/inactive toggle per variant
- ✅ Stock status tracking (in_stock, out_of_stock)
- ✅ Variant selection on product page
- ✅ Variant display in cart/orders
- ✅ **Admin variant management UI** — inline editable retail_price inputs, toggle switches for is_active
- ✅ **Live profit calculation** — color-coded profit & margin columns (green/red)
- ✅ **Bulk operations** — apply percentage markup, activate/deactivate selected variants
- ✅ **Auto-update product price** — product `price` field syncs to min active variant retail_price
- ✅ 7 tests

---

### 15. Reporting & Analytics Dashboard

**Status:** ✅ Complete (February 2026)
**Effort:** 3-4 days
**Business Impact:** Low - Business intelligence

#### Overview
~~Comprehensive reporting dashboard~~ **IMPLEMENTED:** Dedicated order analytics dashboard with revenue metrics, charts, and customer insights.

#### Implemented Features
- ✅ Revenue metrics (total revenue, paid orders, avg order value, total discounts, total tax)
- ✅ Previous period comparison with delta indicators (percentage change)
- ✅ Revenue trend line chart (Chart.js) with daily aggregation
- ✅ Top products by revenue and by quantity sold
- ✅ Customer metrics (new customers, total customers, repeat rate)
- ✅ Top 10 customers by spend
- ✅ Fulfillment status breakdown
- ✅ Payment method breakdown
- ✅ Period selector (30/90/365 days)
- ✅ Admin navigation link with `fas fa-chart-line` icon

#### Additionally Implemented (February 2026)
- ✅ **Date range selector** — pill buttons (7d, 30d, 90d) on admin dashboard
- ✅ **Revenue-by-category chart** — horizontal bar chart using OrderItem join to product_categories
- ✅ **AOV trend** — average order value as second Y-axis on revenue chart
- ✅ **CSV export** — `GET /admin/dashboard/export?period=30` with daily revenue, order count, AOV

#### Still Needed
- Revenue forecasting

---

## Technical Debt & Code Quality

### 16. Automated Testing Suite

**Status:** ✅ Complete (February 2026 - 376 tests passing)
**Effort:** Complete
**Business Impact:** Low - Long-term code quality

#### Overview
~~Comprehensive test coverage for critical functionality.~~ **IMPLEMENTED:** 376 tests passing with PHPUnit 11.5 and SQLite in-memory database.

#### Implemented Features
- ✅ 376 feature and unit tests — all passing
- ✅ 20 model factories for realistic test data
- ✅ Feature tests for checkout, payments, admin CRUD, coupons, reviews, newsletters, search, and more
- ✅ Unit tests for service layer (OrderFactory, PaymentService, CheckoutService)
- ✅ API endpoint tests (products, orders, auth)
- ✅ GitHub Actions CI/CD pipeline
- ✅ PHPUnit 11.5 with in-memory SQLite for fast execution

#### Still Needed
- Browser test execution setup (ChromeDriver for Dusk)
- Performance regression testing

---

### 17. Performance Optimization

**Status:** Partially complete (Feb 2026)
**Effort:** Ongoing
**Business Impact:** Low-Medium - User experience

#### Overview
Database query optimization, caching, and performance monitoring.

#### Core Features
- Database indexing (partially done)
- Eager loading (partially done)
- ✅ GSAP tree-shaking (only core + ScrollTrigger bundled, ~30KB gzipped)
- ✅ GPU-composited animations only (`transform`, `opacity`)
- Redis caching for frequently accessed data
- CDN for static assets
- Image optimization and lazy loading
- Query monitoring and slow query alerts

---

### 18. Accessibility Improvements

**Status:** Mostly complete (Feb 2026 visual redesign + accessibility pass)
**Effort:** 0.5-1 day remaining
**Business Impact:** Low - Compliance and inclusivity

#### Overview
Ensure WCAG 2.1 AA compliance across the site.

#### Core Features
- ✅ `prefers-reduced-motion` media query disables all GSAP + CSS animations
- ✅ Dark mode with system preference detection
- ✅ Focus indicators on all new interactive elements (`focus-visible:ring-2`)
- ✅ ARIA labels on dark mode toggle, mobile nav drawer, back-to-top button
- ✅ ARIA attributes on navigation dropdowns (`aria-haspopup`, `:aria-expanded`)
- ✅ Button `:focus-visible` states with high-contrast mode support (`@media (prefers-contrast: more)`)
- ✅ Removed `onclick="window.location"` anti-pattern - replaced with semantic `<a>` tags (11 occurrences)
- ✅ `x-cloak` on Alpine.js components to prevent flash of unstyled content
- Screen reader compatibility (partial)
- Color contrast compliance (both light and dark modes)
- Skip navigation links
- Alt text for all images

---

## Ideas for Future Consideration

### 19. Mobile App

**Status:** Concept
**Effort:** Several months
**Business Impact:** Unknown - Market dependent

- Native iOS/Android apps or Progressive Web App (PWA)
- Push notifications for appointments and orders
- Faster booking experience
- Mobile-specific features (location services, camera for support tickets)

---

### 20. Service Packages & Bundles

> **Note:** Services are disabled in PrintStore. This feature is not applicable to the print-on-demand store.

**Status:** ✅ Complete (February 2026)
**Effort:** 1-2 weeks
**Business Impact:** Medium - Increases average order value (N/A for PrintStore)

#### Overview
~~Bundle multiple services~~ **IMPLEMENTED:** Admin CRUD for service bundles with customer-facing display and cart integration.

#### Implemented Features
- ✅ `service_bundles` and `service_bundle_items` tables
- ✅ `ServiceBundle` model with `items()`, `active()`, `featured()` scopes, `savings` and `savings_percent` accessors
- ✅ Admin CRUD with stats, price calculator, service selection
- ✅ Customer-facing bundle cards on services index page with savings badges
- ✅ Cart integration for purchasing bundles
- ✅ 5 tests

#### Still Needed
- Subscription-based service plans (monthly maintenance, etc.)
- Pre-paid service credits
- Family/group packages

---

### 21. Referral Program

**Status:** Concept (mentioned in membership plan)
**Effort:** 1 week
**Business Impact:** Medium - Customer acquisition

- Unique referral links for customers
- Reward for successful referrals (credit, discount, free service)
- Track referrals in customer dashboard
- Analytics on referral conversion

---

### 22. Multi-Currency & Internationalization

**Status:** Concept
**Effort:** 2-3 weeks
**Business Impact:** Low - Depends on expansion plans

- Support multiple currencies
- Currency conversion
- Localization (translations, date formats, etc.)
- Multi-language content
- Region-specific pricing

---

### 23. Integration with Third-Party Services

**Status:** Partially Complete (February 2026)
**Effort:** Varies
**Business Impact:** Varies

#### Implemented Integrations
- ✅ **Sales Tax Automation**: TaxJar API integration (real-time tax calculation, nexus compliance, transaction reporting for filing)
- ✅ **Analytics**: Google Analytics 4 (GA4) with e-commerce event tracking
- ✅ **Advertising**: Meta Pixel with conversion tracking (ViewContent, AddToCart, Purchase)
- ✅ **Product Feeds**: Google Shopping XML feed for Google Merchant Center (enhanced Feb 2026 with sale dates, category breadcrumbs, additional images)
- ✅ **Fulfillment**: Printful API integration (order creation, webhook handling, mockup generation)

#### Still Needed
- **Accounting**: QuickBooks, Xero
- **CRM**: Salesforce, HubSpot
- **Calendar**: Google Calendar, Outlook for appointment syncing
- **Communication**: Slack notifications for admins

---

## Admin Workflow Enhancements

**Based on comprehensive assessment (December 2025)** - See `/Users/benjamin/.claude/plans/atomic-crafting-wall.md` for complete details

### 24. Bulk Actions & Automation

**Status:** Not Started
**Effort:** 1-2 weeks (multiple subsystems)
**Business Impact:** High - Saves 30-50% of admin time on routine tasks

#### Overview
Comprehensive bulk operation capabilities across all admin interfaces to streamline workflow and reduce repetitive tasks.

#### Missing Bulk Actions
**Orders:**
- Bulk status updates (mark multiple as "shipped" or "processing")
- Bulk invoice generation and email delivery
- Bulk customer notifications
- Export with custom field selection

**Appointments:**
- Bulk status changes (confirmed → completed)
- Bulk reschedule (weather delays, provider absence)
- Bulk cancellation with automated customer notifications

**Reviews:**
- Bulk approve/reject for pending reviews
- Bulk spam deletion
- Templated bulk admin responses

**Customers:**
- Bulk email to selected segments
- Bulk tag/segment assignment
- Bulk newsletter list assignment

**Products:**
- Bulk price updates (percentage or fixed amount)
- Bulk inventory adjustments with audit logging
- Bulk category assignment/removal
- Bulk discount application

#### Implementation Approach
- Checkbox selection UI pattern (already implemented for products)
- Bulk action dropdown with confirmation modals
- Transaction-based operations with rollback on error
- Comprehensive audit logging for all bulk changes

---

### 25. Advanced Analytics & Reporting

**Status:** ✅ Partially Complete (February 2026 — dashboard enhanced with date range, AOV, CSV export)
**Effort:** 1 week remaining
**Business Impact:** High - Essential for data-driven business decisions

#### Overview
Comprehensive analytics dashboard providing deep insights into customer behavior, product performance, and financial metrics.

#### Core Features

**Customer Analytics:**
- Customer Lifetime Value (CLV) tracking
- Customer Acquisition Cost (CAC) calculation
- Churn analysis (inactive customers identification)
- RFM Analysis (Recency, Frequency, Monetary segmentation)
- Customer geography heat map
- New vs returning customer ratios

**Product Performance:**
- Profit margin analysis (requires cost tracking)
- Product comparison reports
- Revenue trends over time
- Stock turnover rates
- Best-selling designs and categories

**Financial Reporting:**
- Monthly revenue comparison (MoM, YoY)
- Payment method breakdown
- Tax report generation
- Refund/chargeback tracking
- Revenue forecasting (trend-based)
- Profit & loss statements

**Dashboard Enhancements:**
- Date range comparison filters
- Custom date range selector
- Export reports to PDF/CSV
- Scheduled report emails (weekly/monthly summaries)
- Dashboard widget customization (show/hide)

#### Technical Implementation
```
New Controller: app/Http/Controllers/Admin/AnalyticsController.php
New Views: resources/views/admin/analytics/
Database: Add 'cost' column to products table for margin analysis
Charts: Use Chart.js for data visualization
```

---

### 26. Admin Notification System

**Status:** ✅ Complete (February 2026 - Phase 8.1)
**Effort:** 2-3 days
**Business Impact:** High - Prevents missed critical events

#### Overview
~~Real-time and email-based notification system~~ **IMPLEMENTED:** In-app admin notification bell with real-time polling and email-based alerts.

#### Implemented Features
- ✅ `notifications` table with `AdminNotification` model
- ✅ Bell dropdown in admin header with unread count badge
- ✅ Alpine.js polling (30-second refresh interval)
- ✅ Mark as read / mark all as read functionality
- ✅ Notification types: new_order, new_appointment, new_review, low_stock, new_return
- ✅ `AdminNotificationService` with try-catch isolation (failures never crash user operations)
- ✅ Low-stock email alerts via `LowStockNotification` + `CheckLowStock` scheduler

#### Still Needed
- Daily summary digests
- Notification filtering by type
- System health alerts (queue failures, payment gateway downtime)

---

### 27. CSV Import System

**Status:** ✅ Complete (February 2026)
**Effort:** 1-2 days
**Business Impact:** High - Essential for bulk inventory management

#### Overview
~~Bulk data import capability~~ **IMPLEMENTED:** Background job-based CSV import for products and customers with progress tracking.

#### Implemented Features
- ✅ `csv_imports` table tracking import status, progress, and errors
- ✅ `CsvImportService` with `importProducts()` (upsert by SKU) and `importCustomers()` (upsert by email)
- ✅ `ProcessCsvImport` queue job with chunked processing (100 rows per batch)
- ✅ Admin interface: upload form, progress tracking (Alpine.js AJAX polling), error log download
- ✅ Downloadable CSV templates with proper headers (`generateTemplate()`)
- ✅ Stats dashboard (total imports, processing, completed, failed)
- ✅ Error reporting with row-level details (JSON error log)
- ✅ 7 tests

#### Still Needed
- Image import via URLs
- Newsletter list assignment during customer import

---

### 28. Admin Activity Audit Log

**Status:** ✅ Complete (February 2026)
**Effort:** 1-2 days
**Business Impact:** Medium - Security, compliance, accountability

#### Overview
~~Comprehensive logging of all admin actions~~ **IMPLEMENTED:** Audit logging via bootable `Auditable` trait with manual `AuditLog::record()` calls.

#### Implemented Features
- ✅ `audit_logs` table with user_id, action, model_type, model_id, old_values, new_values, IP, user_agent
- ✅ `AuditLog` model with `record()` static method, scopes: `forModel()`, `byUser()`, `action()`, `between()`
- ✅ `Auditable` trait using `static::created/updated/deleted` hooks — auto-logs CRUD
- ✅ Applied to Setting model; other models use manual `AuditLog::record()` in controllers
- ✅ Admin audit log viewer with stats, filters (user, action, model_type, date range, search)
- ✅ Detail view with before/after change diff
- ✅ 5 tests

#### Still Needed
- Export audit logs to CSV for compliance
- Automatic old log cleanup (configurable retention)

---

### 29. Visual Calendar Interface

> **Note:** Appointments/scheduling are disabled in PrintStore. This feature is not applicable to the print-on-demand store.

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 days
**Business Impact:** High - Much better UX than list view (N/A for PrintStore)

#### Overview
~~Calendar-based appointment management~~ **IMPLEMENTED:** FullCalendar.js visual calendar with color-coded appointments and provider filtering.

#### Implemented Features
- ✅ FullCalendar.js via CDN (month/week/day view toggle)
- ✅ `CalendarController` with `index()` and `events()` JSON endpoint
- ✅ Color-coded by status: pending=#F59E0B, confirmed=#10B981, completed=#6B5F4A, cancelled=#EF4444
- ✅ Provider filter dropdown
- ✅ Click event opens appointment detail page
- ✅ 3 tests

#### Still Needed
- Drag-and-drop appointment rescheduling
- Quick appointment creation (click time slot)
- Visual conflict detection warnings

---

### 30. Customer Relationship Management (CRM)

**Status:** ✅ Partially Complete (February 2026 - Tags & Segmentation)
**Effort:** 2-3 days
**Business Impact:** High - Essential for targeted marketing

#### Overview
~~CRM features for customer segmentation~~ **PARTIALLY IMPLEMENTED:** Customer tagging system with tag CRUD, assignment, filtering, and bulk operations.

#### Implemented Features

**Customer Tagging:**
- ✅ `tags` table with name, slug, color, description
- ✅ `customer_tag` pivot table with assigned_by tracking
- ✅ `Tag` model with auto-slug, `customers()` belongsToMany
- ✅ Admin tag CRUD with color picker
- ✅ AJAX toggle tag assignment on customer detail page
- ✅ Bulk tag assignment
- ✅ Customer index filtering by tag
- ✅ 5 default tags seeded: VIP, Wholesale, Brand Fan, Repeat Customer, Newsletter VIP
- ✅ 6 tests

#### Still Needed

**Customer Segmentation:**
- Create segments with multiple conditions
- Export segments to CSV
- Assign segments to newsletter lists

**Lifecycle Stages (Auto-assigned):**
- New, Active, At-Risk, Churned, VIP auto-assignment

**Communication History:**
- Log all emails sent to customer
- Newsletter engagement tracking (opens/clicks)
- Appointment reminder history

---

### 31. Admin Role & Permission System

**Status:** All admins have full access
**Effort:** 3-4 days
**Business Impact:** Medium - Needed for team delegation

#### Overview
Granular role-based access control for multi-admin environments.

#### Roles
- **Super Admin** - Full access to everything
- **Manager** - All features except sensitive settings
- **Staff** - Orders, appointments, customers only
- **Content Manager** - Blog, products, services only

#### Implementation
- Use Spatie Laravel Permission package
- Middleware: `can:manage-products`, `can:view-reports`
- UI automatically hides inaccessible menu items
- Audit log tracks permission checks

---

## Customer Experience Enhancements

**Based on comprehensive assessment (December 2025)** - See `/Users/benjamin/.claude/plans/atomic-crafting-wall.md` for complete details

### 32. Promotional Codes & Coupons

**Status:** ✅ Complete (February 12, 2026)
**Effort:** 2-3 days
**Business Impact:** High - Essential for marketing campaigns

#### Overview
~~Comprehensive discount code system~~ **IMPLEMENTED:** Complete coupon/discount code system with admin management, checkout integration, and usage tracking.

#### Implemented Features
- ✅ Create discount codes in admin (percentage or fixed amount)
- ✅ Minimum order value requirements
- ✅ Multi-use codes with total usage limits
- ✅ Per-customer usage limits
- ✅ Start/expiration dates
- ✅ Active/inactive toggle with quick-toggle action
- ✅ Usage tracking and analytics (coupon_usage table)
- ✅ AJAX code validation at checkout (Alpine.js)
- ✅ Max discount cap for percentage coupons
- ✅ Stripe integration (one-time Stripe Coupon for checkout sessions)
- ✅ CSV export with chunked processing
- ✅ Admin stats dashboard (total, active, expired, total savings)
- ✅ Mobile-responsive admin views (desktop table + mobile cards + FAB filter)
- ✅ 17 tests (admin CRUD, validation, checkout integration)
- ✅ Factory with 8 states, seeder with 8 sample coupons

#### Additionally Implemented (February 2026)
- ✅ **Automatic code application via URL parameter** (e.g., `?promo=SAVE20`) - Phase 6.1

#### Still Needed
- **Campaign analytics** (conversion rates, revenue impact)

---

### 33. Global Search & Advanced Filtering

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 days
**Business Impact:** High - Critical for product/service discovery

#### Overview
~~Unified search across all content types~~ **IMPLEMENTED:** Global search with autocomplete and full results page.

#### Implemented Features
- ✅ `SearchService` with `search($query, $limit)` and `fullSearch($query, $type, $perPage)` methods
- ✅ Searches products, services, and blog posts using LIKE queries
- ✅ Header search with debounced AJAX autocomplete (4 results per category, 300ms)
- ✅ Full results page with tab buttons (All/Products/Services/Blog)
- ✅ Mobile search support in header and mobile nav drawer
- ✅ Glassmorphism design with result cards
- ✅ 5 tests

#### Still Needed
- Price range slider filter
- Rating filter
- Availability filter (in stock, low stock, out of stock)
- Recent search history

---

### 34. Wishlist / Favorites

**Status:** ✅ Complete (February 2026 - Phase 7.1)
**Effort:** 1-2 days
**Business Impact:** High - Re-engagement and conversion tool

#### Overview
~~Save products and services~~ **IMPLEMENTED:** Polymorphic wishlist system for products and services with guest migration.

#### Implemented Features
- ✅ `wishlists` table (polymorphic: products + services)
- ✅ `WishlistController` with AJAX toggle (add/remove)
- ✅ Heart icon on product/service cards
- ✅ Dedicated wishlist page with move-to-cart functionality
- ✅ Guest-to-auth migration via `MigrateGuestWishlist` listener

#### Additionally Implemented (February 2026)
- ✅ **Shareable wishlist links** — `POST /wishlist/share` generates unique 64-char token
- ✅ **Public shared wishlist view** — `GET /wishlist/shared/{token}` read-only grid with "Add to Cart" buttons
- ✅ **Share button UI** — navigator.share() Web Share API with clipboard.writeText() fallback
- ✅ `wishlist_share_token` column on customers table
- ✅ 7 tests

#### Still Needed
- Email reminders when wishlist items go on sale
- Price drop notifications

---

### 35. Customer Address Book & Saved Payments

**Status:** ✅ Partially Complete (February 2026 - Address Book)
**Effort:** 2-3 days
**Business Impact:** High - Reduces checkout friction

#### Overview
~~Save multiple addresses and payment methods~~ **PARTIALLY IMPLEMENTED:** Customer address book with checkout integration.

#### Implemented Features (Address Book)
- ✅ `addresses` table with label, type (shipping/billing/both), is_default, street/city/state/zip/country/phone
- ✅ `Address` model with scopes: `shipping()`, `billing()`, `default()`, auto-unset other defaults
- ✅ `AddressController` with CRUD + `setDefault()` + `jsonIndex()` for checkout AJAX
- ✅ Customer address management page (glassmorphism cards, add/edit forms)
- ✅ Checkout "Select Saved Address" dropdown with Alpine.js auto-fill
- ✅ Dashboard "Manage Addresses" link
- ✅ 5 tests

#### Still Needed (Saved Payment Methods)
- Save credit cards via Stripe (PCI-compliant tokenization)
- Display last 4 digits + brand (Visa, MC, etc.)
- Set default payment method
- Quick one-click checkout for repeat customers

---

### 36. Order Tracking & Status Updates

**Status:** ✅ Complete (February 2026 - Phase 7.3 + Tech Integration)
**Effort:** 2-3 days
**Business Impact:** High - Reduces "where's my order?" support tickets

#### Overview
~~Real-time order tracking~~ **IMPLEMENTED:** Visual order tracking timeline with carrier integration and automated email notifications.

#### Implemented Features
- ✅ `tracking_number`, `tracking_carrier`, `shipped_at`, `delivered_at` fields on orders
- ✅ `OrderStatusMail` for shipped/delivered email notifications with tracking links
- ✅ Carrier URL generation (UPS, FedEx, USPS, DHL) - direct link to carrier tracking page
- ✅ Visual timeline component on order detail page
- ✅ Admin tracking number form for manual entry
- ✅ `fulfillment_status`, `fulfillment_provider`, `fulfillment_order_id` fields
- ✅ Fulfillment routing (Printful or manual) with automatic dispatch

#### Still Needed
- Out for delivery notification
- Estimated delivery date display

---

### 37. Return & Refund Management

**Status:** ✅ Complete (February 2026 - Phase 8.2)
**Effort:** 3-4 days
**Business Impact:** High - Customer trust and retention

#### Overview
~~Customer-initiated return requests~~ **IMPLEMENTED:** Full return/refund management with customer request form and admin workflow.

#### Implemented Features
- ✅ `return_requests` table with `ReturnRequest` model
- ✅ Customer return request form (item selection, reason dropdown, refund method)
- ✅ Admin approval/rejection/completion workflow
- ✅ `ReturnStatusMail` for status update emails
- ✅ Admin returns dashboard with stats (pending, approved, completed, total refunded)
- ✅ Return status tracking (pending → approved/rejected → completed)
- ✅ Admin notifications for new return requests

#### Still Needed
- Upload return photos (optional)
- RMA number generation
- Partial refund support via Stripe API

---

### 38. Appointment Rescheduling

> **Note:** Appointments/scheduling are disabled in PrintStore. This feature is not applicable to the print-on-demand store.

**Status:** ✅ Complete (February 2026 - Phase 7.2)
**Effort:** 1-2 days
**Business Impact:** High - Major UX improvement (N/A for PrintStore)

#### Overview
~~Allow customers to reschedule~~ **IMPLEMENTED:** Dedicated reschedule form with availability checking.

#### Implemented Features
- ✅ Reschedule button on appointment detail page
- ✅ Dedicated reschedule form with pre-filled provider/service
- ✅ Real-time availability checking via existing AJAX API
- ✅ Cancels old appointment + creates new one
- ✅ Change date/time while keeping same provider (or choose different)

---

### 39. Product Recommendations

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 days
**Business Impact:** High - Increases average order value

#### Overview
~~Smart product recommendations~~ **IMPLEMENTED:** Co-purchase and price-proximity recommendations on product detail pages.

#### Implemented Features
- ✅ "Customers Also Bought" - Co-purchase frequency analysis from paid orders (ranked by how often products are bought together)
- ✅ "Similar Products" - Same-category products filtered by price range (50%-200% of current product), ranked by price proximity
- ✅ `RecommendationService` with `getAlsoBought()` and `getSimilarProducts()` methods
- ✅ Graceful degradation (sections hidden when no data available)
- ✅ Integrated into product detail page with `<x-product-card>` component
- ✅ Staggered scroll animations

#### Still Needed
- "You might also like" (browsing history tracking)
- "Complete the look" bundles
- Related services on product pages
- Collaborative filtering algorithm

---

### 40. Notification Preferences

**Status:** No customer control over emails
**Effort:** 1-2 days
**Business Impact:** High - GDPR compliance, customer satisfaction

#### Overview
Allow customers to control which emails they receive.

#### Core Features

**Email Preferences:**
- Order confirmations (required, cannot disable)
- Shipping updates (opt-in)
- Appointment reminders (opt-in)
- Marketing emails/newsletters (opt-in)
- Product back-in-stock alerts (opt-in)
- Review request emails (opt-in)

**Implementation:**
- Add `notification_preferences` JSON column to customers table
- Check preferences before sending each email type
- Preference management page in customer portal

---

### 41. Abandoned Cart Recovery

**Status:** ✅ Complete (February 2026 - Tech Integration)
**Effort:** 2-3 days
**Business Impact:** High - Recovers 10-30% of abandoned carts

#### Overview
~~Automated email campaign~~ **IMPLEMENTED:** Hourly automated email campaign for abandoned cart recovery.

#### Implemented Features
- ✅ `AbandonedCartSequenceMail` parameterized mailable (step 1/2/3) with cart items table
- ✅ `SendAbandonedCartEmails` artisan command (runs hourly) with 3-step sequence
- ✅ **Step 1 (1 hour):** "You left something behind!" — friendly cart reminder
- ✅ **Step 2 (24 hours):** "Still thinking about it?" — social proof, product highlights, customer love
- ✅ **Step 3 (72 hours):** "Last chance: 5% off your cart" — CART5 coupon code with `?promo=CART5` auto-apply
- ✅ Skips customers who placed orders recently
- ✅ `abandoned_cart_email_sent_at`, `abandoned_cart_email_2_sent_at`, `abandoned_cart_email_3_sent_at` tracking columns
- ✅ 7-day max window, previous step required before next step sends
- ✅ CART5 coupon (5% off) seeded in CouponSeeder

#### Still Needed
- Unsubscribe option for abandoned cart emails specifically

---

### 42. Review Request Automation

**Status:** ✅ Complete (February 2026 - Phase 6.3)
**Effort:** 1 day
**Business Impact:** Medium - Increases review count 3-5x

#### Overview
~~Automated email campaign~~ **IMPLEMENTED:** Automated review request emails sent after order delivery.

#### Implemented Features
- ✅ `ReviewRequestMail` sent 7 days after delivery
- ✅ `SendReviewRequests` artisan command (runs daily)
- ✅ Tracks `review_request_sent_at` to prevent duplicate emails
- ✅ Direct link to product review form
- ✅ One-time send per order (no spam)

---

### 43. Back-in-Stock Notifications

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 days
**Business Impact:** High - Recovers lost sales

#### Overview
~~Email alerts when out-of-stock products become available~~ **IMPLEMENTED:** "Notify Me" button on out-of-stock products with automated email alerts.

#### Implemented Features
- ✅ `stock_notifications` table with customer_id (nullable), email, product_id, notified_at
- ✅ `StockNotification` model with `pending()` and `forProduct()` scopes
- ✅ `BackInStockMail` mailable + email template
- ✅ `SendBackInStockNotifications` command — finds restocked products, sends emails, marks notified (every 15 min)
- ✅ "Notify Me" button on product detail page (Alpine.js email form, pre-filled if authenticated)
- ✅ Works for both guests (email required) and authenticated users
- ✅ Admin product detail shows pending subscriber count
- ✅ 5 tests

---

### 44. Reorder Functionality

**Status:** ✅ Complete (December 25, 2025)
**Effort:** 4-6 hours
**Business Impact:** Medium - Convenience feature for repeat customers

#### Overview
One-click reorder from order history.

#### Core Features (Implemented)
- ✅ "Reorder" button on past orders (only for paid orders)
- ✅ Pre-fills cart with same items
- ✅ Stock availability validation (products only)
- ✅ Shows price changes if any
- ✅ Notifies if items no longer available
- ✅ Smart quantity merging if items already in cart
- ✅ Comprehensive feedback messages for unavailable/changed items

#### Implementation Details
**Route:** `POST /orders/{order}/reorder` ([routes/web.php](routes/web.php:151))

**Controller:** [OrderController::reorder()](app/Http/Controllers/Store/OrderController.php:55-131)
- Authorization check (customer owns order)
- Iterates through order items
- Validates item existence and stock availability
- Detects price changes
- Merges with existing cart items or creates new ones
- Returns detailed success/warning messages

**View:** Reorder button in [orders/show.blade.php](resources/views/orders/show.blade.php:226-233)
- Shown only for paid orders
- Uses standard `btn-primary` styling
- CSRF protected form

---

### 45. Invoice/Receipt Download

**Status:** ✅ Complete (February 2026)
**Effort:** 1-2 days
**Business Impact:** Medium - Professional touch, business customer requirement

#### Overview
~~Downloadable PDF invoices~~ **IMPLEMENTED:** DomPDF-generated invoices for paid orders with customer and admin download.

#### Implemented Features
- ✅ `barryvdh/laravel-dompdf` package installed
- ✅ `InvoiceService` with `generateInvoice(Order $order)` → renders PDF view, returns download response
- ✅ `resources/views/pdf/invoice.blade.php` — standalone HTML with inline CSS (DomPDF compatible)
- ✅ Business logo/address pulled from Settings
- ✅ Itemized breakdown with taxes and totals
- ✅ Customer download: "Download Invoice" button on order detail (paid orders only)
- ✅ Admin download: "Download Invoice" button on admin order detail
- ✅ Authorization via OrderPolicy (customers can only download their own)
- ✅ 6 tests

---

### 46. Account Security Dashboard

**Status:** Basic profile management exists
**Effort:** 2-3 days
**Business Impact:** Medium - Security-conscious customers

#### Overview
Security features for account protection.

#### Core Features
- Login history (last 10 logins with IP, device, timestamp)
- Active sessions management
- Two-factor authentication (2FA) setup
- Password strength indicator
- Security alerts (login from new device)

---

### 46.5. Cookie Consent Banner

**Status:** ✅ Complete (February 2026)
**Effort:** 2-3 hours
**Business Impact:** Medium - Legal requirement in EU/UK, GDPR/CCPA compliance

#### Overview
~~GDPR/CCPA-compliant cookie consent banner~~ **IMPLEMENTED:** Glassmorphism slide-up banner with Accept/Decline functionality.

#### Implemented Features
- ✅ Alpine.js glassmorphism slide-up banner
- ✅ Accept/Decline buttons with localStorage persistence (`cookie_consent` key)
- ✅ Link to Privacy Policy page
- ✅ ARIA role="dialog" for accessibility
- ✅ x-cloak + x-transition animations for smooth appearance
- ✅ Session-based dismissal (shown only once per user session)
- ✅ Dark mode support via CSS custom properties

#### Implementation Details
- **Component:** `resources/views/components/cookie-consent.blade.php`
- **Location:** Included in `layouts/app.blade.php` after email popup
- **Dismissal:** Clicking Accept/Decline stores preference in localStorage and hides banner permanently

---

### 47. GDPR Data Export

**Status:** ✅ Complete (February 2026)
**Effort:** 1 day
**Business Impact:** Medium - Legal requirement in EU/UK

#### Overview
~~Allow customers to download all their data~~ **IMPLEMENTED:** Background ZIP generation with signed download URLs.

#### Implemented Features
- ✅ `data_exports` table tracking status, file path, expiry
- ✅ `DataExportService` generates ZIP with JSON files: profile, orders, appointments, reviews, wishlist, addresses, loyalty_points, newsletter_subscriptions
- ✅ `GenerateDataExport` queue job (timeout 300s)
- ✅ `DataExportReadyMail` with signed download URL
- ✅ `CleanExpiredExports` command (daily cleanup)
- ✅ "Export My Data" button on customer dashboard
- ✅ Throttled: 1 request per 24 hours
- ✅ Compliant with GDPR Article 20 (Right to Data Portability)
- ✅ 4 tests

---

## Enhanced Technical Debt & Code Quality

### 48. Performance Optimizations

**Status:** Some optimizations done (indexes, eager loading)
**Effort:** 1-2 weeks (ongoing)
**Business Impact:** Medium-High - Page load speed, server costs

#### Additional Optimizations Needed

**Database Indexes:**
```sql
-- Reviews (improve admin filtering)
ALTER TABLE reviews ADD INDEX idx_reviews_reviewable (reviewable_type, reviewable_id);
ALTER TABLE reviews ADD INDEX idx_reviews_status_created (status, created_at DESC);

-- Cart (improve session lookups)
ALTER TABLE cart ADD INDEX idx_cart_session (session_id);

-- Orders (improve webhook lookups)
ALTER TABLE orders ADD INDEX idx_orders_stripe_intent (stripe_payment_intent_id);

-- Newsletter (improve subscriber queries)
ALTER TABLE newsletter_subscriptions ADD INDEX idx_subscriptions_email (email);
```

**Query Result Caching:**
- Services by category (1 hour cache)
- Provider availability for next 7 days
- Product catalog for category pages
- Blog posts list

**Email Queue Jobs:**
- Convert synchronous email sends to queued jobs
- Prevents blocking HTTP responses
- Retry logic for failed sends

---

### 49. Comprehensive Test Suite

**Status:** ✅ Largely Complete (February 2026 - 376 tests)
**Effort:** 1-2 weeks
**Business Impact:** Low (immediate) - High (long-term stability)

#### Implemented Test Coverage
- ✅ **376 tests passing** across all major features
- ✅ 20 model factories for generating realistic test data
- ✅ Feature tests covering: checkout, appointments, reviews, newsletters, admin CRUD, coupons, API, search, loyalty, audit log, tags, CSV import, bundles, calendar, addresses, invoices, GDPR export, back-in-stock, wishlist, returns, memberships
- ✅ Unit tests for service layer (OrderFactory, PaymentService, CheckoutService, etc.)
- ✅ Browser tests created (20 Dusk tests, requires ChromeDriver setup)
- ✅ PHPUnit 11.5 with in-memory SQLite for fast execution
- ✅ GitHub Actions CI/CD pipeline

#### Still Needed
- Browser test execution setup (ChromeDriver)
- Performance regression testing
- Load testing

---

### 50. API Development

**Status:** ✅ Complete (February 2026)
**Effort:** 1-2 weeks
**Business Impact:** Low - Enables mobile app, integrations

#### Overview
~~Internal AJAX only~~ **IMPLEMENTED:** RESTful API with Sanctum token authentication and versioned endpoints.

#### Implemented Features
- ✅ Laravel Sanctum token authentication (`HasApiTokens` on Customer model)
- ✅ Versioned endpoints at `/api/v1/`
- ✅ API Resources: `ProductResource`, `ServiceResource`, `OrderResource`, `OrderItemResource`
- ✅ `ProductApiController` — `index()` paginated + filters, `show()` (scoped to active)
- ✅ `ServiceApiController` — same pattern
- ✅ `OrderApiController` — authenticated, own orders only, OrderPolicy authorization
- ✅ `AuthApiController` — `login()` returns Sanctum token, `logout()` revokes, `user()` returns profile
- ✅ Rate limiting: `throttle:60,1` public, `throttle:30,1` auth endpoints
- ✅ 8 tests (ProductApiTest, OrderApiTest, AuthApiTest)

#### Still Needed
- API documentation (Swagger/OpenAPI)
- Appointment booking endpoint
- Review submission endpoint

---

### 51. Monitoring & Error Tracking

**Status:** Laravel logs only
**Effort:** 2-3 days
**Business Impact:** Medium - Catch issues before users report them

#### Recommended Tools
- **Error Tracking:** Sentry or Bugsnag
- **APM:** New Relic, DataDog, or self-hosted
- **Queue Monitoring:** Laravel Horizon
- **Uptime Monitoring:** Pingdom, UptimeRobot

#### Custom Metrics
- Daily revenue
- Conversion rate
- Cart abandonment rate
- Average order value
- Email delivery rates

---

## Enhancement Request Process

If you'd like to propose a new enhancement:

1. Add it to this document with:
   - Clear title and overview
   - Estimated effort (if known)
   - Business impact assessment
   - Core features list

2. Prioritize using these criteria:
   - **High**: Critical for business goals or blocking other features
   - **Medium**: Valuable but not urgent, can be scheduled
   - **Low**: Nice-to-have, implement when resources available

3. Update status as work progresses:
   - **Not Started** - No work begun
   - **Concept** - Idea stage, needs detailed planning
   - **Fully Planned** - Detailed plan exists, ready to implement
   - **In Progress** - Currently being developed
   - **Complete** - Implemented and deployed

---

**Note:** This document is a living record. As priorities shift and new opportunities arise, enhancements may be re-prioritized or replaced.
