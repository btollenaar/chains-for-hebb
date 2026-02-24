# PrintStore — Offline Launch Checklist

**Last Updated:** February 18, 2026
**Purpose:** Everything you need to do *outside* the codebase before launching.

---

## 1. Legal & Business Formation

- [ ] **Form LLC** — Register your desired business name (e.g., "YourStore LLC") with Oregon Secretary of State
  - [Oregon Business Registry](https://sos.oregon.gov/business/Pages/register.aspx) — **$100 filing fee**
  - File Articles of Organization online
  - Designate a Registered Agent (must have a physical Oregon street address — no PO Box)
  - Options: Be your own agent (use home address) or use a service ($50-150/year)
- [ ] **Oregon Annual Report** — Due every year on your LLC anniversary date
  - **$100/year** — Filed with Oregon Secretary of State
  - Can file online; late filing results in administrative dissolution
- [ ] **Get EIN** — Apply for Employer Identification Number (free, instant)
  - [IRS EIN Application](https://www.irs.gov/businesses/small-businesses-self-employed/apply-for-an-employer-identification-number-ein-online)
  - Required for: business bank account, tax filings, Stripe
- [ ] **Business License** — Check your city/county requirements
  - **Clackamas County (unincorporated areas): No business license required**
  - If you're within city limits (e.g., Oregon City, Milwaukie, Lake Oswego), check that city's requirements
  - No state-level general business license in Oregon
- [ ] **Oregon Has NO Sales Tax** — You do **not** need a state sales tax permit
  - Oregon is one of 5 states with no state sales tax
  - You will NOT collect sales tax on orders shipped to Oregon customers
  - However, you MUST collect sales tax in other states where you have **economic nexus** (see Section 5)
- [ ] **Oregon Corporate Activity Tax (CAT)** — Only if gross revenue exceeds **$1 million**
  - $250 flat + 0.57% on commercial activity over $1M
  - Not relevant at launch, but be aware as you scale
  - [Oregon CAT Info](https://www.oregon.gov/dor/programs/businesses/Pages/corporate-activity-tax.aspx)

---

## 2. Business Banking & Finance

- [ ] **Open Business Bank Account** — Keep personal and business finances separate
  - Bring: EIN letter, Articles of Organization, ID
  - Recommended: Chase, Bank of America, or a local credit union
- [ ] **Get Business Credit Card** — For inventory purchases, ad spend, subscriptions
- [ ] **Set Up Accounting** — QuickBooks Self-Employed or QuickBooks Online ($30/mo)
  - Track income, expenses, sales tax collected
  - Connect to business bank account for auto-categorization
- [ ] **Determine Sales Tax Nexus** — Know which states you must collect tax in
  - **Oregon has no sales tax** — you have no home-state sales tax obligation
  - Economic nexus: States where you exceed sales thresholds (typically $100k or 200 transactions/year — varies by state)
  - As sales grow, you'll need to register in states where you hit economic nexus thresholds
  - TaxJar handles calculation, but you must register in each nexus state to legally collect

---

## 3. Domain & Hosting

> **Note:** You already have a DreamHost account. Use it for domain registration, hosting, SSL, DNS, and email. See `DEPLOYMENT-GUIDE.md` for detailed DreamHost deployment steps.

- [ ] **Register Domain** — Purchase your production domain via DreamHost
  - DreamHost Panel → Domains → Registrations
  - e.g., `yourdomain.com` or similar
  - Free WHOIS privacy included with DreamHost registration
- [ ] **Add Hosting for Domain** — DreamHost Panel → Manage Websites → Add Website
  - Select your registered domain
  - Ensure PHP 8.2+ is selected (DreamHost Panel → PHP version)
  - Enable Passenger if available, or configure document root to `public/`
- [ ] **SSL Certificate** — Enable free Let's Encrypt SSL
  - DreamHost Panel → Manage Websites → your domain → HTTPS tab
  - Toggle on "Secure Hosting" (auto-provisions Let's Encrypt)
- [ ] **Configure DNS** — Automatic if domain is registered at DreamHost
  - If domain is registered elsewhere, point nameservers to DreamHost:
    - `ns1.dreamhost.com`, `ns2.dreamhost.com`, `ns3.dreamhost.com`
- [ ] **Set Up Email** — DreamHost includes free email hosting
  - DreamHost Panel → Mail → Manage Email
  - Create: `hello@yourdomain.com`, `support@yourdomain.com`
  - Alternative: Google Workspace ($7/mo) for Gmail interface + calendar
- [ ] **Set Up SSH Access** — Required for deployment
  - DreamHost Panel → Manage Users → Edit user → Enable SSH/Shell access
  - See `DEPLOYMENT-GUIDE.md` for SSH key setup
- [ ] **Set Up Cron Job** — Required for Laravel scheduler
  - DreamHost Panel → Advanced → Cron Jobs
  - Command: `cd /home/username/yourdomain.com && php artisan schedule:run >> /dev/null 2>&1`
  - Frequency: Every minute (`* * * * *`)

---

## 4. Payment Processing — Stripe

- [ ] **Create Stripe Account** — [stripe.com](https://stripe.com)
  - Verify business identity (EIN, business address, bank account)
  - Verification can take 1-3 business days
- [ ] **Get Live API Keys** — Dashboard → Developers → API Keys
  - `STRIPE_KEY` — Publishable key (starts with `pk_live_`)
  - `STRIPE_SECRET` — Secret key (starts with `sk_live_`)
- [ ] **Set Up Webhook Endpoint** — Dashboard → Developers → Webhooks
  - URL: `https://yourdomain.com/stripe/webhook`
  - Events to listen for:
    - `checkout.session.completed`
    - `payment_intent.succeeded`
    - `payment_intent.payment_failed`
    - `customer.subscription.created` (memberships)
    - `customer.subscription.updated`
    - `customer.subscription.deleted`
    - `invoice.payment_succeeded`
    - `invoice.payment_failed`
  - Copy `STRIPE_WEBHOOK_SECRET` (starts with `whsec_`)
- [ ] **Configure Branding** — Stripe Dashboard → Settings → Branding
  - Upload logo, set brand color, business name
  - This shows on the Stripe Checkout page your customers see

---

## 5. Sales Tax — TaxJar

- [ ] **Create TaxJar Account** — [taxjar.com](https://www.taxjar.com)
  - Starter plan: ~$19/mo (up to 200 orders)
  - Provides real-time sales tax calculation by jurisdiction
- [ ] **Get API Key** — TaxJar Dashboard → Account → API
  - `TAXJAR_API_KEY` — Your API token
  - Set `TAXJAR_SANDBOX=false` for production
- [ ] **Add Nexus States** — TaxJar Dashboard → State Settings
  - **Do NOT add Oregon** (Oregon has no sales tax)
  - Add only states where you have economic nexus (e.g., if you ship 200+ orders to Washington or exceed $100k in California)
  - Most common nexus states for Pacific NW e-commerce: Washington, California
- [ ] **Register for Sales Tax in Nexus States** — Must do this yourself
  - TaxJar calculates & reports, but you must be registered in each state to legally collect
  - Start with states where you expect the most sales volume

---

## 6. Email Service (Transactional)

Your app sends many automated emails. You need a reliable email provider.

- [ ] **Choose Email Provider** — Pick one:
  - **DreamHost SMTP** — Free with your hosting, use your DreamHost email credentials (simplest setup, fine for low volume)
  - **Mailgun** — $0.80/1000 emails, great Laravel integration (recommended for higher volume)
  - **SendGrid** — Free tier (100 emails/day), good deliverability
  - **AWS SES** — Cheapest at scale ($0.10/1000 emails), more setup
  - **Postmark** — Best deliverability, $1.25/1000 emails
- [ ] **Get API Credentials** — Set in `.env`:
  - `MAIL_MAILER` — smtp (or mailgun, ses, postmark)
  - `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
  - `MAIL_FROM_ADDRESS` — e.g., `noreply@yourdomain.com`
- [ ] **Verify Sending Domain** — Add DNS records (SPF, DKIM, DMARC)
  - Prevents emails from going to spam
  - If using DreamHost SMTP: SPF/DKIM are auto-configured for DreamHost-hosted domains
  - If using third-party (Mailgun, etc.): Add their DNS records via DreamHost Panel → DNS
- [ ] **Test Email Delivery** — Send test emails to Gmail, Outlook, Yahoo
  - Verify they don't land in spam

---

## 7. Analytics & Marketing Pixels

### Google Analytics 4
- [ ] **Create GA4 Property** — [analytics.google.com](https://analytics.google.com)
  - Create account → Create property → Set up web data stream
  - Copy Measurement ID (format: `G-XXXXXXXXXX`)
  - Set `GOOGLE_ANALYTICS_ID` in `.env`

### Google Merchant Center (for Shopping ads)
- [ ] **Create Merchant Center Account** — [merchants.google.com](https://merchants.google.com)
  - Verify and claim your website
  - Your app auto-generates the product feed at `/feed/google-shopping.xml`
  - Submit feed URL in Merchant Center
  - Set `GOOGLE_MERCHANT_CENTER_ID` in `.env`

### Meta (Facebook/Instagram) Pixel
- [ ] **Create Meta Pixel** — [Facebook Events Manager](https://business.facebook.com/events_manager)
  - Create a pixel → Copy Pixel ID (numeric string)
  - Set `META_PIXEL_ID` in `.env`
  - The app auto-tracks: ViewContent, AddToCart, InitiateCheckout, Purchase

### Google Ads (optional)
- [ ] **Create Google Ads Account** — Link to Merchant Center for Shopping campaigns
- [ ] **Set Up Conversion Tracking** — Import from GA4 or add Google Ads tag

---

## 8. Fulfillment & Suppliers

### Printful (Print-on-Demand) — Required for PrintStore
- [ ] **Create Printful Account** — [printful.com](https://www.printful.com)
  - Design products (t-shirts, totes, mugs, hoodies, posters with your branding)
  - Get API key: Dashboard → Settings → API
  - Set `PRINTFUL_API_KEY` in `.env`
- [ ] **Configure Printful Webhook** — Dashboard → Settings → Notifications → Webhooks
  - URL: `https://yourdomain.com/webhooks/printful`
  - Set `PRINTFUL_WEBHOOK_SECRET` in `.env`
  - Events: `package_shipped`, `order_failed`, `order_canceled`
  - **Important:** The webhook URL must be publicly accessible (not localhost). Set this up after deploying to production.
- [ ] **Upload Designs** — Create your product designs in Printful
  - Upload print files for each product (front, back, sleeve, etc.)
  - Generate mockups for storefront display
  - Sync products to your store via the Printful API
- [ ] **Test Fulfillment** — Place a test order to verify end-to-end flow
  - Printful offers sample orders at a discount
  - Verify: order dispatched, tracking received via webhook, customer notified

### If Self-Fulfilling (Hybrid)
- [ ] **Source Inventory** — Find wholesale product suppliers
  - Examples: Faire, Tundra, or direct from manufacturers
- [ ] **Shipping Accounts** — Set up business accounts for discounted rates
  - [ ] USPS Business account (or Stamps.com / Pirate Ship for discounts)
  - [ ] UPS account (optional)
  - [ ] FedEx account (optional)
- [ ] **Shipping Scale** — For accurate weight-based shipping costs
- [ ] **Return Address Labels** — Print return labels for customer returns

---

## 9. Business Insurance

- [ ] **General Liability Insurance** — Protects against customer claims
  - Covers: bodily injury, property damage, advertising injury
  - Cost: ~$400-800/year for small e-commerce
  - Providers: Hiscox, Next Insurance, The Hartford
- [ ] **Product Liability Insurance** — If selling physical goods
  - Covers: claims that your products caused harm
  - Often bundled with general liability
- [ ] **Cyber Liability Insurance** — Protects against data breaches
  - Covers: customer data exposure, credit card breach
  - Cost: ~$500-1500/year
  - Recommended since you handle payment data (even via Stripe)

---

## 10. Legal Pages Content

The app has templates for these pages, but you should have a lawyer review them or customize the specifics:

- [ ] **Privacy Policy** — Update with your actual:
  - Business name and contact info
  - Specific data you collect
  - Third-party services you share data with (Stripe, TaxJar, GA4, Meta, etc.)
  - CCPA/CPRA specifics if selling to California residents
- [ ] **Terms of Service** — Update with your actual:
  - Governing law (Oregon)
  - Dispute resolution / arbitration clause
  - Limitation of liability
  - Intellectual property ownership
- [ ] **Return & Refund Policy** — Customize:
  - Return window (currently 30 days in template)
  - Who pays return shipping
  - Non-returnable items
  - Refund method (original payment, store credit)
- [ ] **Shipping Policy** — Customize:
  - Processing time
  - Free shipping threshold (currently $75 in app)
  - International shipping (if applicable)
  - Shipping carrier(s) used

---

## 11. Content & Branding

- [ ] **Logo** — Professional logo design
  - Options: 99designs, Fiverr, local designer
  - Need: Full logo, icon/favicon version, white version for dark backgrounds
  - Formats: SVG (web), PNG (transparency), favicon.ico
- [ ] **Brand Colors** — Finalize your palette
  - Update via Admin → Settings → Theme in the app
- [ ] **Product Photography / Mockups** — High-quality product images
  - Printful generates mockups automatically for print-on-demand products
  - For custom photos: order samples and photograph them
  - Recommended: White background, lifestyle shots, detail shots
  - Minimum: 800x800px, ideally 1200x1200px
- [ ] **About Page Content** — Write your brand story
  - Your mission. Your values. Why you started.
  - Upload via Admin → Settings → Profile
- [ ] **Blog Content** — Prepare 3-5 launch posts
  - Examples: "Our Story", "Behind the Designs", "New Collection Drop"
  - Helps with SEO and gives the site substance at launch
- [ ] **Social Media Profiles** — Create accounts
  - [ ] Instagram (essential for merch/apparel)
  - [ ] Facebook Page
  - [ ] TikTok (great for merch brands)
  - [ ] Pinterest (optional)
  - Add URLs in Admin → Settings → Social Media

---

## 12. Pre-Launch Testing

- [ ] **Test Stripe Payments** — Process a real transaction ($1) and refund it
  - Verify webhook fires correctly
  - Verify order confirmation email sends
- [ ] **Test Email Delivery** — Trigger each email type:
  - [ ] Order confirmation
  - [ ] Welcome email sequence
  - [ ] Abandoned cart (add item, wait 1 hour)
  - [ ] Back-in-stock notification
  - [ ] Account claim (guest checkout)
- [ ] **Test on Mobile** — Check entire flow on phone
  - Browse → Add to Cart → Checkout → Payment → Confirmation
- [ ] **Test Guest Checkout** — Complete purchase without account
- [ ] **Test Registered Checkout** — Complete purchase with account + loyalty points
- [ ] **Verify Legal Pages** — All 4 legal pages load and have correct content
- [ ] **Check SSL** — Verify https:// works and no mixed content warnings (DreamHost Let's Encrypt)
- [ ] **Verify Cron/Scheduler** — Ensure Laravel scheduler is running on DreamHost
  - Should already be configured in Section 3 (DreamHost Panel → Advanced → Cron Jobs)
  - Verify it's running: check `storage/logs/laravel.log` for scheduler output

---

## 13. Launch Day

- [ ] **Switch to Production Mode**
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `TAXJAR_SANDBOX=false`
- [ ] **Switch Stripe to Live Keys** — Replace `pk_test_` / `sk_test_` with `pk_live_` / `sk_live_`
- [ ] **Run Production Migrations** — `php artisan migrate --force`
- [ ] **Seed Production Data** — Only core seeders (CustomerSeeder, SettingsSeeder)
  - Do NOT run test data seeders (DummyUsersSeeder, etc.)
- [ ] **Set Up Real Admin Account** — Change default admin credentials
- [ ] **Configure Production Caching**
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- [ ] **Verify Backup System** — Set up automated database backups
  - DreamHost includes automatic daily backups (Panel → Manage Websites → Backups)
  - Additional: manual export or spatie/laravel-backup package for offsite copies
- [ ] **Monitor Logs** — Watch `storage/logs/laravel.log` for first 48 hours

---

## 14. Post-Launch

- [ ] **Submit to Google Search Console** — [search.google.com/search-console](https://search.google.com/search-console)
  - Verify ownership, submit sitemap
- [ ] **Submit Google Shopping Feed** — In Merchant Center, add feed URL:
  - `https://yourdomain.com/feed/google-shopping.xml`
- [ ] **Set Up Google Ads** — If running paid advertising
- [ ] **Set Up Meta Ads** — If running Facebook/Instagram ads
- [ ] **Set Up Uptime Monitoring** — Get alerted if site goes down
  - Free: UptimeRobot, Better Uptime
  - Your app has `/health` endpoint for monitoring
- [ ] **Weekly Tasks:**
  - Review orders and fulfill/ship
  - Check low-stock alerts
  - Review customer feedback/reviews
  - Check analytics (GA4 dashboard)
  - Monitor email deliverability
- [ ] **Monthly Tasks:**
  - File/remit sales tax in nexus states (no Oregon sales tax, but check other states where you collect)
  - Reconcile QuickBooks
  - Review ad spend vs revenue
  - Write 1-2 blog posts for SEO

---

## Quick Reference: All API Keys Needed

| Service | Env Variable | Where to Get It | Required? |
|---------|-------------|-----------------|-----------|
| Stripe (payments) | `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` | [stripe.com/dashboard](https://dashboard.stripe.com) | **Yes** |
| TaxJar (sales tax) | `TAXJAR_API_KEY` | [app.taxjar.com](https://app.taxjar.com) | Recommended |
| Email (SMTP) | `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` | Your email provider | **Yes** |
| Google Analytics | `GOOGLE_ANALYTICS_ID` | [analytics.google.com](https://analytics.google.com) | Recommended |
| Meta Pixel | `META_PIXEL_ID` | [business.facebook.com](https://business.facebook.com) | Optional |
| Google Merchant | `GOOGLE_MERCHANT_CENTER_ID` | [merchants.google.com](https://merchants.google.com) | Optional |
| Printful | `PRINTFUL_API_KEY`, `PRINTFUL_WEBHOOK_SECRET` | [printful.com](https://www.printful.com) | **Yes** |

---

## Estimated Startup Costs

| Item | Cost | Frequency |
|------|------|-----------|
| LLC Formation (Oregon) | $100 | One-time |
| Oregon Annual Report | $100 | Annual |
| Domain Name (DreamHost) | $10-15 | Annual |
| Hosting (DreamHost — existing account) | $3-17/mo | Monthly |
| SSL Certificate (DreamHost Let's Encrypt) | Free | — |
| Email (DreamHost built-in) | Free | — |
| Stripe | 2.9% + $0.30/transaction | Per sale |
| TaxJar | $19/mo | Monthly |
| Email Service (Mailgun — if needed) | ~$15/mo | Monthly (optional) |
| Business Insurance | $400-800 | Annual |
| Logo Design | $50-500 | One-time |
| Product Photography | $0-500 | One-time |
| **Total to Launch** | **~$200-650 + monthly ~$22-50** | |

---

*This checklist covers the business operations side. For technical deployment, see [DEPLOYMENT-GUIDE.md](DEPLOYMENT-GUIDE.md).*
