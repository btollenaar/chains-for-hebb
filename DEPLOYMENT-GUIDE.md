# DreamHost Deployment Guide - PrintStore (POD Merch Store)

**Last Updated:** February 19, 2026
**Application Version:** Production-Ready (Architecture Refactored + Visual Redesign + Site Audit Complete + Comprehensive Test Suite)
**Deployment Target:** DreamHost Shared Hosting
**Domain:** bentollenaar.dev (production)

---

## 🔥 Critical Updates

**Recent Improvements - DEPLOY IMMEDIATELY:**

### February 2026

8. **Complete UI/UX Overhaul (February 24, 2026)**
   - **SettingsComposer fix:** Added 6 missing feature flags (donations, events, gallery, fundraising_tracker, sponsors, cms_pages) — unlocks full navigation
   - **Logo rebrand:** Replaced PrintStore PNG logos with Chains for Hebb SVG logos (light + dark mode variants with chain link icon)
   - **Hero section:** Full-viewport hero with Gemini-generated forest background, dark overlay, GSAP entrance animations, inline progress snippet, scroll indicator
   - **AI-generated imagery:** 9 site images generated via Google Gemini (hero, mission aerial, donate basket, events community, progress construction, 4 gallery placeholders) saved as WebP to `public/images/generated/`
   - **Design system CSS:** Section backgrounds (nature, elevated, dark), enhanced card borders, progress bar shimmer animation, donate button pulse, event type badges, milestone timeline styles
   - **Homepage redesign:** Side-by-side mission section, calendar-style event date blocks, sponsor tier display, dark CTA banner, forest-silhouette newsletter section, sticky mobile donate bar
   - **Page improvements:** Donate (hero banner, quick amount buttons, inline progress), Progress (revenue breakdown cards, stacked budget bar chart, visual milestone timeline), Events (hero, type badges, "Next Event" highlight), Gallery (photo count badges, hover overlays)
   - **Navigation:** CMS pages moved to "More" dropdown to prevent nav overflow
   - **Email popup:** Rebranded to forest green, triggers on scroll (past hero) instead of timer, copy updated for fundraiser context
   - **Alpine.js Intersect:** Added for scroll-triggered progress bar animation and counter
   - **Image generation script:** `scripts/generate-images.py` — reads GEMINI_API_KEY from .env, generates all site imagery via Gemini 2.0 Flash
   - **Status:** Build successful, all changes visual/frontend only

7. **Product Edit Page Redesign (February 19, 2026)**
   - Restructured `/admin/products/{id}/edit` from single-column flat layout to 2-column card-based layout
   - Matches the Printful catalog setup page pattern: numbered step cards, sticky sidebar, product header with image
   - Conditional Printful sections: read-only pricing summary, POD inventory badge, variants/designs/mockups outside main form
   - Admin-teal focus rings throughout, hidden inputs for Printful-managed fields
   - Layout-only change — no functionality modified
   - **Status:** All tests passing (367 pass, 9 skipped, 0 failures)

6. **POD Seed Data Overhaul (February 19, 2026)**
   - Replaced 52 generic categories (Electronics, Home & Garden, Books, etc.) with 20 POD categories
   - Replaced 100 generic products (zero variants) with 36 Printful POD products + 264 size/color variants
   - All products have proper Printful fields: `fulfillment_type`, `printful_product_id`, `base_cost`, `profit_margin`
   - Admin controller 404s now redirect gracefully with flash message instead of throwing exceptions
   - **Status:** All tests passing (367 pass, 9 skipped, 0 failures)

5. **Printful Integration Audit (February 19, 2026)**
   - **Critical:** Stripe webhook now dispatches FulfillOrder (orders no longer lost if customer closes browser)
   - **Critical:** FulfillOrder confirms Printful orders (no longer left as unfulfilled drafts)
   - **Critical:** Expanded fulfillment_status enum to include 'shipped', 'delivered', 'failed' (webhooks no longer crash)
   - **High:** Default fulfillment provider changed to 'printful' (POD-exclusive store)
   - **High:** ShippingService fetches live rates from Printful API with hardcoded fallback
   - **Medium:** Fixed mockup generation (uses file URL instead of file ID)
   - **Medium:** Fixed product setup form variant index bug (sparse array handling)
   - **Medium:** FulfillOrder marks orders as 'failed' on missing SKUs instead of silent return
   - **Status:** All 376 tests passing

4. **Site Audit & Remediation (February 12, 2026)**
   - **Critical:** Fixed `staff_members` → `providers` table references (6 places) — prevented SQL errors on availability/note queries
   - **Critical:** Reviews section rewritten for full dark mode support using CSS custom properties
   - **High:** Removed console.error statements from production JS (5 locations)
   - **High:** Added ARIA attributes to navigation dropdowns (WCAG 2.1 Level A)
   - **Medium:** Phone validation hardened, conditional Vimeo loading, dead CSS removed
   - **Commit:** `4b0e52a`
   - **Status:** ✅ All 376 tests passing (1159 assertions)

### January 2026

1. **Critical Bug Fix (January 5, 2026)** - Stripe Payment Integration
   - **Issue:** PaymentService was accessing non-existent `snapshot` array, causing all Stripe checkouts to fail with fatal error
   - **Fix:** Corrected field access to use `$item->name`, `$item->unit_price` instead of `$item->snapshot['name']`, `$item->snapshot['price']`
   - **Impact:** 100% of Stripe payments would fail without this fix
   - **Commit:** `4b042b2` - Includes 41 comprehensive unit tests for service layer
   - **Status:** ✅ All 376 tests passing

2. **HTMLPurifier Cache Directory Fix (January 4, 2026)**
   - Automatic directory creation prevents CI/production failures
   - Self-healing across all environments
   - **Commit:** `762f503`

3. **Architecture Refactoring (January 3, 2026)**
   - Service Layer Pattern implementation (OrderFactory, PaymentService, CheckoutService)
   - Form Request validation classes
   - Trait-based model composition
   - **Commit:** `6c25330`

**Test Coverage:** 376 tests (1151 assertions) - 367 passing, 9 skipped in ~5 seconds (parallel mode)

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [DreamHost Account Setup](#dreamhost-account-setup)
4. [GitHub Authentication](#github-authentication)
5. [Initial Server Setup](#initial-server-setup)
6. [Application Deployment](#application-deployment)
7. [Environment Configuration](#environment-configuration)
8. [Database Setup](#database-setup)
9. [Frontend Asset Building](#frontend-asset-building)
10. [Stripe Configuration](#stripe-configuration)
11. [Email Configuration](#email-configuration)
12. [Storage & File Uploads](#storage--file-uploads)
13. [Security Hardening](#security-hardening)
14. [Testing Procedures](#testing-procedures)
15. [Go-Live Checklist](#go-live-checklist)
16. [Troubleshooting](#troubleshooting)
17. [Maintenance & Updates](#maintenance--updates)

---

## Executive Summary

### Application Overview

**PrintStore** is a print-on-demand merch store built with Laravel 11, combining:

- **E-commerce System** - Product catalog with inventory tracking, shopping cart, Stripe payment processing
- **Printful Integration** - Print-on-demand fulfillment with product variants, designs, and mockups
- **Content Management** - Blog system, about page, newsletter campaigns
- **Customer Portal** - Order history, profile updates, wishlist
- **Admin Dashboard** - Full CRUD management for products, orders, customers, Printful catalog
- **Advanced Features**:
  - **Hierarchical category system** with unlimited nesting
  - **Multiple category assignment** per product with primary designation
  - **Collapsible tree UI** with visual hierarchy for category selection
  - **Responsive admin tables** - Mobile-first design with FAB filters and card layouts
  - **Newsletter campaign management** - Email campaigns with open/click tracking, subscriber lists
  - **Review system** - Customer reviews with moderation and admin responses
  - **Guest cart migration** - Seamless cart transfer on login/registration
  - **Real-time AJAX cart** with instant feedback
  - **WYSIWYG editor** for content (TinyMCE)
  - **Global notification system**
  - **XSS protection** (HTMLPurifier)
  - **Mobile-responsive throughout**

### Technology Stack

- **Backend:** Laravel 11 (PHP 8.4)
- **Frontend:** Tailwind CSS v4, Alpine.js, Vite, GSAP (ScrollTrigger)
- **Design System:** Glassmorphism, dark mode, CSS custom properties, fluid typography (Inter + Space Grotesk)
- **Database:** MySQL 8.0+
- **Payment:** Stripe API (Products + Payment Intents)
- **Authentication:** Laravel Breeze
- **Image Processing:** Intervention Image
- **Security:** HTMLPurifier for XSS prevention
- **Email:** SMTP (configurable), Mailable classes with responsive templates

### Deployment Strategy

This guide covers deployment to **DreamHost Shared Hosting** with:
- SSH access enabled
- Git-based deployment workflow
- Manual Composer/npm builds
- MySQL database provisioning
- SSL certificate (Let's Encrypt)
- Custom domain configuration

**Estimated Deployment Time:** 2-3 hours (first-time deployment)

---

## Pre-Deployment Checklist

### Development Environment Requirements

- [ ] All features tested locally and working correctly
- [ ] **Test suite passing:** 376 tests (1151 assertions) - Run: `php artisan test --parallel`
- [ ] Database seeded with production-ready content (admin user, categories, products)
- [ ] `.env.template` file reviewed and ready to copy
- [ ] `composer.lock` and `package-lock.json` committed to Git
- [ ] All migrations tested and can run fresh successfully
- [ ] Storage directory structure tested (product images, settings)
- [ ] **Stripe test mode transactions verified** (critical: test checkout flow end-to-end)
- [ ] Admin interface fully functional and mobile-responsive
- [ ] Customer-facing pages tested across devices
- [ ] XSS protection verified (HTMLPurifier working)
- [ ] Git repository pushed to GitHub/private repo
- [ ] **Latest commit includes PaymentService bug fix** (`4b042b2` or later)

### Required Credentials & Accounts

- [ ] DreamHost account with shared hosting plan
- [ ] Domain name registered and DNS configured
- [ ] GitHub account with repository access
- [ ] Stripe account (production API keys)
- [ ] Email service credentials (SMTP server, username, password)
- [ ] SSL certificate provisioned (DreamHost provides free Let's Encrypt)

### Documentation Preparation

- [ ] Review `README.md` for complete feature list
- [ ] Check `CLAUDE.md` for architecture patterns and security notes
- [ ] Verify `config/business.php` has correct business information
- [ ] Confirm all environment-specific settings documented

---

## DreamHost Account Setup

### 1. Enable SSH Access

1. Log in to DreamHost Panel: https://panel.dreamhost.com
2. Navigate to **Users** → **Manage Users**
3. Edit your user account
4. Change user type to **Shell User** (if not already)
5. Set protocol to **SSH** (not SFTP)
6. Save changes and wait 5-10 minutes for propagation

### 2. Connect via SSH

```bash
# Replace with your actual DreamHost credentials
ssh username@servername.dreamhost.com

# Example:
ssh youruser@youruser.dreamhosters.com
```

**Verify Connection:**
```bash
# Check shell type (should be bash or similar)
echo $SHELL

# Check current directory (should be /home/username)
pwd

# Check available disk space
df -h ~
```

### 3. Configure Domain

1. In DreamHost Panel: **Domains** → **Manage Domains**
2. Add domain: `yourdomain.com`
3. Set web directory to: `/home/username/yourdomain.com/public`
4. Enable **HTTPS** (free Let's Encrypt SSL)
5. Enable **PHP 8.2** or higher
6. Wait 15-30 minutes for DNS propagation

---

## GitHub Authentication

### Option 1: Personal Access Token (Recommended)

**Why PAT:** SSH keys on shared hosting can be problematic; PAT is more reliable.

#### Generate PAT on GitHub

1. Log in to GitHub
2. Go to **Settings** → **Developer settings** → **Personal access tokens** → **Tokens (classic)**
3. Click **Generate new token (classic)**
4. Configure token:
   - **Note:** "DreamHost Deployment - Laravel Business Platform"
   - **Expiration:** 90 days (or No expiration for convenience, but less secure)
   - **Scopes:** Check `repo` (full control of private repositories)
5. Click **Generate token**
6. **IMPORTANT:** Copy the token immediately (you won't see it again)

#### Configure Git Credentials on DreamHost

```bash
# SSH into DreamHost
ssh username@servername.dreamhost.com

# Configure Git to cache credentials
git config --global credential.helper store

# Set your GitHub username
git config --global user.name "Your Name"
git config --global user.email "your-email@example.com"

# Clone repository (will prompt for credentials)
cd ~
git clone https://github.com/yourusername/your-repo-name.git yourdomain.com

# When prompted:
# Username: your-github-username
# Password: paste-your-PAT-token-here

# Credentials will be stored in ~/.git-credentials
```

### Option 2: SSH Keys (Alternative)

If you prefer SSH keys:

```bash
# Generate SSH key on DreamHost
ssh-keygen -t ed25519 -C "dreamhost-deployment"

# Display public key
cat ~/.ssh/id_ed25519.pub

# Copy output and add to GitHub:
# GitHub → Settings → SSH and GPG keys → New SSH key
```

---

## Initial Server Setup

### 1. Clone Repository

```bash
# SSH into DreamHost
ssh username@servername.dreamhost.com

# Navigate to home directory
cd ~

# Clone repository to domain directory
git clone https://github.com/yourusername/your-repo-name.git yourdomain.com

# Navigate to project directory
cd yourdomain.com

# Verify files
ls -la
```

### 2. Set Proper Permissions

```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
chmod -R 775 storage bootstrap/cache

# If using Git-based deployment, ensure .git is protected
chmod -R 700 .git
```

### 3. Verify PHP Version

```bash
# Check PHP version (should be 8.2+)
php -v

# Check PHP modules
php -m | grep -E 'pdo|mysql|mbstring|xml|curl|zip|gd'

# If PHP version is wrong, add to .bash_profile:
echo 'export PATH=/usr/local/php84/bin:$PATH' >> ~/.bash_profile
source ~/.bash_profile
php -v
```

---

## Application Deployment

### 1. Install Composer Dependencies

```bash
cd ~/yourdomain.com

# Download Composer (if not already installed)
curl -sS https://getcomposer.org/installer | php

# Move to bin directory for global access
mkdir -p ~/bin
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer

# Add to PATH (if not already)
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bash_profile
source ~/.bash_profile

# Install dependencies (production mode)
composer install --no-dev --optimize-autoloader

# Verify installation
ls -la vendor/
```

**Expected Output:** `vendor/` directory with Laravel, Stripe, HTMLPurifier, etc.

### 2. Set Up Environment File

```bash
# Copy template environment file
cp .env.template .env

# Edit environment file
nano .env
```

**Critical `.env` Settings** (see [Environment Configuration](#environment-configuration) for full details):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=mysql.yourdomain.com
DB_DATABASE=yourusername_dbname
DB_USERNAME=yourusername_dbuser
DB_PASSWORD=your_secure_password

STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

### 3. Generate Application Key

```bash
php artisan key:generate

# Verify key was added to .env
grep APP_KEY .env
```

### 4. Create Storage Symlink

```bash
# Create symbolic link from public/storage to storage/app/public
php artisan storage:link

# Verify symlink
ls -la public/storage
```

---

## Environment Configuration

### Complete `.env` File Template

```env
# ============================================
# APPLICATION SETTINGS
# ============================================
APP_NAME="PrintStore"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=America/Los_Angeles
APP_URL=https://yourdomain.com

# ============================================
# BUSINESS PROFILE
# ============================================
BUSINESS_TYPE=ecommerce
BUSINESS_NAME="PrintStore"
BUSINESS_TAGLINE="Custom merch, made on demand."

# ============================================
# CONTACT INFORMATION
# ============================================
BUSINESS_EMAIL=info@yourdomain.com
BUSINESS_PHONE=555-123-4567
BUSINESS_ADDRESS_STREET="123 Main Street"
BUSINESS_ADDRESS_CITY="Anytown"
BUSINESS_ADDRESS_STATE=FL
BUSINESS_ADDRESS_ZIP=12345

# ============================================
# DATABASE CONFIGURATION
# ============================================
DB_CONNECTION=mysql
DB_HOST=mysql.yourdomain.com
DB_PORT=3306
DB_DATABASE=yourusername_dbname
DB_USERNAME=yourusername_dbuser
DB_PASSWORD=STRONG_PASSWORD_HERE

# ============================================
# CACHE & SESSION
# ============================================
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.yourdomain.com

# ============================================
# QUEUE CONFIGURATION
# ============================================
QUEUE_CONNECTION=database

# ============================================
# STRIPE PAYMENT PROCESSING
# ============================================
# Production keys (get from https://dashboard.stripe.com/apikeys)
STRIPE_KEY=pk_live_51XXXXX
STRIPE_SECRET=sk_live_51XXXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXXX

# Test mode (for staging environment)
# STRIPE_KEY=pk_test_51XXXXX
# STRIPE_SECRET=sk_test_51XXXXX
# STRIPE_WEBHOOK_SECRET=whsec_XXXXX

# ============================================
# EMAIL CONFIGURATION
# ============================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.dreamhost.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=EMAIL_PASSWORD_HERE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# ============================================
# FILESYSTEM
# ============================================
FILESYSTEM_DISK=public

# ============================================
# LOGGING
# ============================================
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# ============================================
# VITE (PRODUCTION BUILD)
# ============================================
VITE_APP_NAME="${APP_NAME}"
```

### Security Notes

- **Never commit `.env` to Git** (already in `.gitignore`)
- **Use strong database passwords** (minimum 16 characters, mixed case, symbols)
- **Rotate Stripe webhook secret** if ever exposed
- **Set `APP_DEBUG=false`** in production (prevents error message leaks)
- **Set `APP_ENV=production`** (disables dev-only features)
- **Use `LOG_CHANNEL=daily`** in production to rotate logs and prevent disk space issues

---

## Database Setup

### 1. Create MySQL Database via DreamHost Panel

1. Log in to DreamHost Panel
2. Navigate to **MySQL Databases**
3. Click **Create New Database**
4. Fill in details:
   - **Database Name:** `yourusername_businessdb`
   - **Hostname:** Auto-generated (e.g., `mysql.yourdomain.com`)
   - **First User:** Create new user
   - **Username:** `yourusername_dbuser`
   - **Password:** Generate strong password
5. Click **Create Database**
6. Note down all credentials for `.env` file

**Important:** DreamHost database names must follow the format `username_databasename`

### 2. Verify Database Connection

```bash
# SSH into DreamHost
cd ~/yourdomain.com

# Test database connection
php artisan db:show

# Should display database name, connection type, tables count
```

**Troubleshooting Connection Issues:**

```bash
# If connection fails, check MySQL is reachable
mysql -h mysql.yourdomain.com -u yourusername_dbuser -p

# Enter password when prompted
# If successful, you'll see mysql> prompt
# Exit with: exit
```

### 3. Run Database Migrations

```bash
# Run all migrations (creates tables)
php artisan migrate --force

# The --force flag is required in production
# It prevents the "Are you sure?" prompt
```

**Expected Output:**
```
Migration table created successfully.
Migrating: 2024_01_01_000000_create_password_reset_tokens_table
Migrated:  2024_01_01_000000_create_password_reset_tokens_table (123.45ms)
...
```

**Verify Tables Created:**

```bash
# List all tables
php artisan db:table

# Or via MySQL directly
mysql -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb -e "SHOW TABLES;"
```

### 4. Seed Database with Production Data

```bash
# Run core production seeders only
php artisan db:seed --class=CustomerSeeder              # Creates default admin
php artisan db:seed --class=SettingsSeeder              # Default settings
php artisan db:seed --class=AboutSeeder                 # Seeds About page content

# Optional: Add product categories
php artisan db:seed --class=ComprehensiveProductCategorySeeder

# Optional: Add sample blog posts
php artisan db:seed --class=BlogSeeder
```

**Important Seeders:**

- `CustomerSeeder` - Creates default admin account (harry.admin@printstore.com / password)
- `SettingsSeeder` - Creates all default application settings
- `AboutSeeder` - Seeds About page content
- `ComprehensiveProductCategorySeeder` - Creates 20 POD product categories (4 top-level + 16 subcategories)

**DO NOT run `DatabaseSeeder`** in production as it includes comprehensive test data (36 products with variants, 12 test users).

### 5. Create/Update Admin Account

```bash
# Option 1: Via Tinker
php artisan tinker

# In Tinker shell:
$admin = \App\Models\Customer::create([
    'name' => 'Admin User',
    'email' => 'admin@yourdomain.com',
    'phone' => '555-123-4567',
    'password' => bcrypt('SECURE_PASSWORD_HERE'),
    'role' => 'admin',
    'is_admin' => true,
    'email_verified_at' => now()
]);

# Exit Tinker
exit
```

**Generate Bcrypt Password:**
```bash
php artisan tinker
bcrypt('your-password-here')
# Copy the output hash
exit
```

---

## Frontend Asset Building

### 1. Install Node.js on DreamHost

DreamHost shared hosting may have an older Node.js version. Install Node Version Manager (nvm):

```bash
# Install nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

# Load nvm into current session
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Add to .bash_profile for future sessions
echo 'export NVM_DIR="$HOME/.nvm"' >> ~/.bash_profile
echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> ~/.bash_profile

# Install Node.js LTS
nvm install --lts

# Verify installation
node -v  # Should show v20.x or higher
npm -v   # Should show v10.x or higher
```

### 2. Install npm Dependencies

```bash
cd ~/yourdomain.com

# Install all npm packages
npm install

# This will create node_modules/ directory
# Expect 5-10 minutes for installation
```

### 3. Build Production Assets

```bash
# Build for production (minified, optimized)
npm run build

# This creates:
# - public/build/assets/app-[hash].js
# - public/build/assets/app-[hash].css
# - public/build/manifest.json
```

**Verify Build Output:**

```bash
ls -la public/build/

# Should see:
# - assets/ directory with JS and CSS files
# - manifest.json (Vite manifest for asset loading)
```

**Important:** The `manifest.json` file tells Laravel which versioned assets to load.

### 4. Handle Build Failures

If build fails due to memory limits on shared hosting:

```bash
# Option 1: Build locally and commit to Git
# On your local machine:
npm run build
git add public/build
git commit -m "Add production build assets"
git push

# On DreamHost:
git pull origin main

# Option 2: Increase Node memory limit
export NODE_OPTIONS="--max-old-space-size=2048"
npm run build
```

---

## Stripe Configuration

### 1. Get Production API Keys

1. Log in to Stripe Dashboard: https://dashboard.stripe.com
2. Toggle **Test mode OFF** (top-right)
3. Navigate to **Developers** → **API keys**
4. Copy:
   - **Publishable key** (starts with `pk_live_`)
   - **Secret key** (starts with `sk_live_`)
5. Add to `.env`:

```env
STRIPE_KEY=pk_live_51XXXXX
STRIPE_SECRET=sk_live_51XXXXX
```

### 2. Create Webhook Endpoint

1. In Stripe Dashboard: **Developers** → **Webhooks**
2. Click **Add endpoint**
3. Configure:
   - **Endpoint URL:** `https://yourdomain.com/stripe/webhook`
   - **Events to send:** Select:
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`
     - `checkout.session.completed`
4. Click **Add endpoint**
5. Copy **Signing secret** (starts with `whsec_`)
6. Add to `.env`:

```env
STRIPE_WEBHOOK_SECRET=whsec_XXXXX
```

### 3. Verify Webhook Endpoint

```bash
# Test webhook endpoint is accessible
curl https://yourdomain.com/stripe/webhook

# Should return 200 OK (even if empty response)
```

In Stripe Dashboard:
1. Go to webhook endpoint details
2. Click **Send test webhook**
3. Choose `payment_intent.succeeded`
4. Check that webhook shows "Succeeded" status

### 4. Update Stripe Settings

In Stripe Dashboard → **Settings**:

1. **Business settings**
   - Business name: Your Business Name
   - Support email: info@yourdomain.com
   - Support phone: 555-123-4567

2. **Branding**
   - Upload logo
   - Set brand colors (match your theme)

3. **Email settings**
   - Customize email receipts with logo and colors

---

## Email Configuration

### Option 1: DreamHost SMTP (Recommended)

Create email account in DreamHost Panel:

1. **Email** → **Manage Email**
2. Create mailbox: `noreply@yourdomain.com`
3. Set strong password
4. Configure `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.dreamhost.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=email_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 2: Third-Party SMTP (SendGrid, Mailgun, etc.)

If higher deliverability needed:

**SendGrid Example:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxx_your_api_key_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Test Email Configuration

```bash
# Via Tinker
php artisan tinker

# Send test email
Mail::raw('Test email from production', function($message) {
    $message->to('admin@yourdomain.com')
            ->subject('Production Email Test');
});

# Exit Tinker
exit

# Check email inbox for test message
```

---

## Storage & File Uploads

### 1. Verify Storage Directory Structure

```bash
cd ~/yourdomain.com

# Check storage structure
ls -la storage/app/public/

# Should have:
# - products/
# - categories/
# - settings/
```

If directories missing:

```bash
mkdir -p storage/app/public/{products,categories,settings}
chmod -R 775 storage/app/public
```

### 2. Verify Storage Symlink

```bash
# Check symlink exists
ls -la public/storage

# Should show: storage -> ../storage/app/public

# If missing, create it
php artisan storage:link
```

### 3. Set Proper Permissions

```bash
# Storage must be writable by web server
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Verify
ls -la storage/
```

### 4. Test File Upload

1. Log in to admin panel: `https://yourdomain.com/admin`
2. Navigate to **Products** → **New Product**
3. Upload a test image
4. Save product
5. Verify image displays on product page
6. Check file was created:

```bash
ls -la storage/app/public/products/
```

---

## Security Hardening

### 1. Protect Sensitive Files

```bash
cd ~/yourdomain.com

# Ensure .env is not publicly accessible
chmod 600 .env

# Protect Git directory
chmod -R 700 .git

# Verify public/ is the only accessible directory
# (DreamHost configuration should handle this)
```

### 2. Disable Directory Listing

Create/verify `.htaccess` in `public/` directory:

```apache
# public/.htaccess
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 3. Verify SSL Certificate

```bash
# Check SSL status
curl -I https://yourdomain.com

# Should show: HTTP/2 200 (not HTTP/1.1)
# Header should include: strict-transport-security
```

In DreamHost Panel:
1. **Domains** → **Manage Domains**
2. Click **Edit** next to your domain
3. Ensure **Secure Hosting (HTTPS)** is enabled
4. Verify certificate is valid (should show Let's Encrypt)

### 4. Configure Security Headers

Add to `public/.htaccess` (after existing rules):

```apache
# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>
```

### 5. Verify HTMLPurifier is Active

```bash
# Check HTMLPurifier service is registered
php artisan tinker

$purifier = app(\App\Services\HtmlPurifierService::class);
echo $purifier->sanitize('<script>alert("XSS")</script><p>Safe content</p>');

# Should output: <p>Safe content</p> (script tag removed)

exit
```

---

## Testing Procedures

### Pre-Deployment Automated Test Checklist

**Run these automated tests BEFORE every deployment to production. All tests must pass.**

#### 1. Run Full Test Suite with Coverage

```bash
# Run all feature and unit tests with coverage reporting
php artisan test --coverage --min=80

# Expected: All tests pass, coverage ≥80%
```

**What This Tests:**
- Guest cart migration (security critical)
- Stripe webhook verification and processing
- Payment flows and order creation
- Authorization policies (role-based access)
- Admin CRUD operations
- File upload security
- N+1 query prevention

**If Tests Fail:**
- DO NOT deploy to production
- Fix failing tests first
- Re-run test suite until all pass

#### 2. Run Browser Tests (Dusk)

```bash
# Test critical user journeys in browser
php artisan dusk

# Expected: All browser tests pass
```

**What This Tests:**
- Complete checkout flow (guest and authenticated)
- Admin product management UI
- Mobile navigation and responsiveness
- Cart and checkout AJAX interactions

**If Dusk Tests Fail:**
- Check ChromeDriver version matches Chrome
- Review screenshot in `tests/Browser/screenshots/`
- Fix UI/JavaScript issues
- Re-run Dusk tests

#### 3. Check Database Migrations

```bash
# Verify all migrations run successfully
php artisan migrate:status

# Expected: All migrations show "Ran" status
```

**If Migrations Missing:**
- Run pending migrations: `php artisan migrate`
- Never run `migrate:fresh` in production (data loss!)

#### 4. Verify Queue Workers (Production Only)

```bash
# Check queue jobs are processing
php artisan queue:work --once

# Expected: No errors, jobs process successfully
```

**For Newsletter Campaigns:**
```bash
# Test scheduler can find scheduled newsletters
php artisan newsletters:send-scheduled

# Expected: Scheduled newsletters dispatched to queue
```

#### 5. Test Critical Endpoints

```bash
# Test homepage loads
curl -I https://yourdomain.com | grep "200 OK"

# Test admin login page
curl -I https://yourdomain.com/login | grep "200 OK"

# Test API availability endpoint
curl https://yourdomain.com/api/availability/dates | jq
```

#### 6. Verify Environment Configuration

```bash
# Check critical environment variables
php artisan config:show | grep -E "APP_ENV|APP_DEBUG|DB_DATABASE|STRIPE_KEY"

# Expected:
# APP_ENV=production
# APP_DEBUG=false
# DB_DATABASE=your_production_db
# STRIPE_KEY=pk_live_... (NOT test key)
```

**Critical Settings:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- Stripe live keys (NOT `pk_test_` or `sk_test_`)
- Production database credentials
- Real email service (NOT `MAIL_MAILER=log`)

#### 7. Security Verification

```bash
# Check for exposed .env file (should return 404)
curl -I https://yourdomain.com/.env

# Verify HTTPS enforced
curl -I http://yourdomain.com | grep "301\|302"

# Test CSRF protection (should return 419)
curl -X POST https://yourdomain.com/admin/products
```

#### 8. Code Quality Checks (Optional but Recommended)

```bash
# Run Laravel Pint for code style (if installed)
./vendor/bin/pint --test

# Check for debug statements
grep -r "dd(" app/ resources/
grep -r "dump(" app/ resources/

# Expected: No dd() or dump() in production code
```

#### Quick Pre-Deployment Test Script

Save this as `scripts/pre-deploy-test.sh`:

```bash
#!/bin/bash

echo "🧪 Running Pre-Deployment Tests..."

# 1. PHPUnit Tests
echo "1️⃣ Running PHPUnit test suite..."
php artisan test --coverage --min=80 || exit 1

# 2. Dusk Browser Tests
echo "2️⃣ Running browser tests..."
php artisan dusk || exit 1

# 3. Migration Status
echo "3️⃣ Checking migrations..."
php artisan migrate:status || exit 1

# 4. Environment Check
echo "4️⃣ Verifying environment..."
if grep -q "APP_DEBUG=true" .env; then
    echo "❌ ERROR: APP_DEBUG is true in .env"
    exit 1
fi

if grep -q "APP_ENV=local" .env; then
    echo "❌ ERROR: APP_ENV is local in .env"
    exit 1
fi

# 5. Check for debug statements
echo "5️⃣ Scanning for debug statements..."
if grep -r "dd(" app/ resources/ | grep -v "addDefinedEntity"; then
    echo "❌ ERROR: Found dd() statements in code"
    exit 1
fi

echo "✅ All pre-deployment tests passed!"
echo "🚀 Ready to deploy to production"
```

**Usage:**
```bash
chmod +x scripts/pre-deploy-test.sh
./scripts/pre-deploy-test.sh
```

**Important:** Only deploy if the script exits with "✅ All pre-deployment tests passed!"

### 1. Basic Functionality Tests (Manual Testing)

#### Homepage Test
- [ ] Navigate to `https://yourdomain.com`
- [ ] Verify hero section displays
- [ ] Check navigation menu works
- [ ] Test mobile menu (hamburger icon)
- [ ] Verify category dropdowns work (desktop hover, mobile accordion)

#### Authentication Test
- [ ] Click **Login**
- [ ] Log in with admin account
- [ ] Verify redirected to dashboard
- [ ] Check admin navigation appears
- [ ] Test logout functionality

#### Admin Panel Test
- [ ] Navigate to `/admin/dashboard`
- [ ] Check all navigation links work:
  - [ ] Customers
  - [ ] Products
  - [ ] Orders
  - [ ] Reviews
  - [ ] Blog
  - [ ] Newsletter (Campaigns, Subscribers, Lists)
  - [ ] Printful Catalog
  - [ ] Email Previews
  - [ ] About
  - [ ] Settings
- [ ] Test filter functionality on list pages
- [ ] Verify pagination works
- [ ] Test mobile admin interface (cards, FAB filters)

#### Product Management Test
- [ ] Navigate to **Products** → **New Product**
- [ ] Create test product with:
  - Name, description, price
  - Upload image
  - Assign to multiple categories (test checkbox tree)
  - Set stock quantity
- [ ] Save and verify appears on products page
- [ ] Edit product and change details
- [ ] Delete product (should soft delete)

### 2. E-commerce Flow Test

#### Add to Cart
- [ ] Browse products as guest
- [ ] Add product to cart (test AJAX notification)
- [ ] Verify cart count badge updates
- [ ] Check cart page shows correct items

#### Checkout Process
- [ ] Proceed to checkout
- [ ] Fill in customer information
- [ ] Enter test credit card (Stripe test mode if testing):
  - Card: 4242 4242 4242 4242
  - Expiry: Any future date
  - CVC: Any 3 digits
- [ ] Complete payment
- [ ] Verify redirected to success page
- [ ] Check order appears in admin panel
- [ ] Verify order confirmation email received

#### Inventory Deduction
- [ ] Verify product stock quantity decreased
- [ ] Try to order more than available stock (should fail)

### 3. Newsletter Campaign Test

- [ ] Log in as admin
- [ ] Navigate to **Newsletter** → **Campaigns**
- [ ] Create new campaign
- [ ] Use TinyMCE editor to create email content
- [ ] Select subscriber list
- [ ] Send test email to admin
- [ ] Verify test email received and displays correctly
- [ ] Check tracking pixel and unsubscribe link work

### 5. Stripe Webhook Test

```bash
# Trigger a test webhook from Stripe Dashboard
# Or use Stripe CLI:

# Install Stripe CLI (on local machine, not DreamHost)
brew install stripe/stripe-cli/stripe

# Forward webhooks to production
stripe listen --forward-to https://yourdomain.com/stripe/webhook

# Trigger test payment
stripe trigger payment_intent.succeeded

# Check logs
tail -f ~/yourdomain.com/storage/logs/laravel.log
```

In Stripe Dashboard:
1. Go to **Webhooks** → Your endpoint
2. Click **Send test webhook**
3. Choose `payment_intent.succeeded`
4. Verify shows "Succeeded" status

### 6. Mobile Responsiveness Test

Test on devices:
- [ ] iPhone (Safari)
- [ ] Android (Chrome)
- [ ] Tablet (iPad)

Check:
- [ ] Navigation menu (hamburger icon)
- [ ] Category dropdowns (collapsible accordion on mobile)
- [ ] Product cards layout
- [ ] Checkout form
- [ ] Admin interface (mobile cards, FAB filters)
- [ ] Image uploads

### 7. Security Test

#### XSS Protection
- [ ] Try to save product with `<script>alert('XSS')</script>` in description
- [ ] Verify script tag is removed (HTMLPurifier)
- [ ] Check frontend displays safe content only

#### SQL Injection Prevention
- [ ] Try to search/filter with SQL injection attempts
- [ ] Verify Laravel's query builder prevents injection

#### Authorization
- [ ] Log in as regular customer
- [ ] Try to access `/admin` (should redirect)
- [ ] Try to view another customer's order (should 403)

---

## Go-Live Checklist

### Pre-Launch (1-2 Days Before)

- [ ] All testing procedures completed successfully
- [ ] Admin account credentials secured (password manager)
- [ ] Stripe production keys verified and tested
- [ ] Email notifications working (order confirmations, shipping updates)
- [ ] SSL certificate active and valid
- [ ] Database backed up
- [ ] `.env` file reviewed (no test/debug settings)
- [ ] Storage permissions verified (775)
- [ ] Frontend assets built and loading correctly
- [ ] Mobile responsiveness verified on real devices
- [ ] Browser testing (Chrome, Safari, Firefox, Edge)
- [ ] Guest cart migration tested (add items as guest, login, verify cart persists)

### Launch Day

- [ ] **Final backup** of database and files:

```bash
# Backup database
mysqldump -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb > ~/backups/pre-launch-db-$(date +%Y%m%d).sql

# Backup files
tar -czf ~/backups/pre-launch-files-$(date +%Y%m%d).tar.gz ~/yourdomain.com
```

- [ ] Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

- [ ] Run final migrations (if any):

```bash
php artisan migrate --force
```

- [ ] Verify DNS propagation (use dnschecker.org)
- [ ] Test site from multiple locations/networks
- [ ] Monitor error logs for first hour:

```bash
tail -f ~/yourdomain.com/storage/logs/laravel.log
```

### Post-Launch (First 24 Hours)

- [ ] Monitor Stripe Dashboard for transactions
- [ ] Check email inbox for customer notifications
- [ ] Review admin panel for orders
- [ ] Verify webhook events in Stripe Dashboard
- [ ] Test customer registration and login
- [ ] Check analytics (Google Analytics, if configured)
- [ ] Respond to any customer support inquiries
- [ ] Document any issues or bugs found

### Post-Launch (First Week)

- [ ] Daily check of error logs
- [ ] Weekly database backup schedule implemented
- [ ] Monitor site performance (page load times)
- [ ] Review security logs for suspicious activity
- [ ] Check SSL certificate status
- [ ] Verify all scheduled tasks running (newsletter scheduler if using cron)
- [ ] Customer feedback collection

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Symptoms:** White screen, "500 Internal Server Error"

**Causes & Solutions:**

1. **Missing `.env` file:**
   ```bash
   # Verify .env exists
   ls -la ~/yourdomain.com/.env

   # If missing, copy from template
   cp .env.template .env
   php artisan key:generate
   ```

2. **File permissions:**
   ```bash
   # Fix storage permissions
   chmod -R 775 storage bootstrap/cache
   ```

3. **Check error logs:**
   ```bash
   tail -50 ~/yourdomain.com/storage/logs/laravel.log
   ```

4. **PHP version mismatch:**
   ```bash
   # Check PHP version
   php -v

   # If wrong, add to .bash_profile:
   echo 'export PATH=/usr/local/php84/bin:$PATH' >> ~/.bash_profile
   source ~/.bash_profile
   ```

### Issue: Assets Not Loading (404 on CSS/JS)

**Symptoms:** Unstyled page, JavaScript not working

**Solutions:**

1. **Rebuild assets:**
   ```bash
   cd ~/yourdomain.com
   npm run build
   ```

2. **Check manifest.json exists:**
   ```bash
   cat public/build/manifest.json
   ```

3. **Verify APP_URL in .env matches domain:**
   ```env
   APP_URL=https://yourdomain.com
   ```

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

### Issue: Database Connection Failed

**Symptoms:** "SQLSTATE[HY000] [2002] Connection refused"

**Solutions:**

1. **Verify database credentials in `.env`:**
   ```bash
   grep DB_ .env
   ```

2. **Test direct MySQL connection:**
   ```bash
   mysql -h mysql.yourdomain.com -u yourusername_dbuser -p
   ```

3. **Check database exists:**
   ```bash
   mysql -h mysql.yourdomain.com -u yourusername_dbuser -p -e "SHOW DATABASES;"
   ```

4. **Verify user has permissions:**
   ```bash
   # In MySQL shell:
   SHOW GRANTS FOR 'yourusername_dbuser'@'%';
   ```

### Issue: Images Not Uploading

**Symptoms:** Image upload form submits but images don't appear

**Solutions:**

1. **Check storage symlink:**
   ```bash
   ls -la public/storage
   # Should point to ../storage/app/public

   # Recreate if missing:
   php artisan storage:link
   ```

2. **Verify directory permissions:**
   ```bash
   chmod -R 775 storage/app/public
   ls -la storage/app/public/
   ```

3. **Check disk space:**
   ```bash
   df -h ~
   ```

4. **Test image upload in admin panel** and check logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Issue: Stripe Webhooks Not Working

**Symptoms:** Orders complete but status not updating

**Solutions:**

1. **Verify webhook endpoint is accessible:**
   ```bash
   curl https://yourdomain.com/stripe/webhook
   ```

2. **Check webhook secret in `.env`:**
   ```bash
   grep STRIPE_WEBHOOK_SECRET .env
   ```

3. **Test webhook from Stripe Dashboard:**
   - Go to Webhooks → Your endpoint
   - Click "Send test webhook"
   - Check response status

4. **Review webhook logs in Stripe Dashboard:**
   - Look for failed webhook attempts
   - Check error messages

5. **Verify route is registered:**
   ```bash
   php artisan route:list | grep stripe
   ```

### Issue: Email Not Sending

**Symptoms:** Order confirmations or shipping updates not received

**Solutions:**

1. **Test email configuration:**
   ```bash
   php artisan tinker

   Mail::raw('Test email', function($message) {
       $message->to('admin@yourdomain.com')
               ->subject('Email Test');
   });

   exit
   ```

2. **Check SMTP credentials in `.env`:**
   ```bash
   grep MAIL_ .env
   ```

3. **Verify email account exists** in DreamHost Panel

4. **Check Laravel logs for email errors:**
   ```bash
   grep -i "mail\|smtp" storage/logs/laravel.log
   ```

5. **Test SMTP connection directly:**
   ```bash
   telnet smtp.dreamhost.com 587
   # Should connect successfully
   ```

### Issue: Slow Page Load Times

**Symptoms:** Pages take 5+ seconds to load

**Solutions:**

1. **Enable caching:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize Composer autoloader:**
   ```bash
   composer dump-autoload --optimize
   ```

3. **Check database query performance:**
   ```bash
   # Temporarily enable query logging in .env:
   APP_DEBUG=true
   LOG_LEVEL=debug

   # Review logs for slow queries
   # Remember to disable debug mode after testing
   ```

4. **Use CDN for assets** (Cloudflare, etc.)

### Issue: Session/CSRF Token Mismatch

**Symptoms:** "CSRF token mismatch" error on form submission

**Solutions:**

1. **Check `SESSION_DOMAIN` in `.env`:**
   ```env
   SESSION_DOMAIN=.yourdomain.com
   ```

2. **Clear browser cookies** and try again

3. **Verify session files are being created:**
   ```bash
   # If using database sessions:
   php artisan tinker
   DB::table('sessions')->count();
   exit
   ```

4. **Check session permissions (if using file driver):**
   ```bash
   chmod -R 775 storage/framework/sessions
   ```

---

## Maintenance & Updates

### Regular Maintenance Tasks

#### Daily
- [ ] Monitor error logs for unusual activity
- [ ] Check Stripe Dashboard for transactions
- [ ] Review customer support inquiries

#### Weekly
- [ ] Database backup
- [ ] Review storage disk usage
- [ ] Check for Laravel security updates
- [ ] Review application performance

#### Monthly
- [ ] Update Composer dependencies (security patches)
- [ ] Review and renew SSL certificate (auto-renewed by Let's Encrypt)
- [ ] Audit user accounts and permissions
- [ ] Review and optimize database indexes

### Database Backup Strategy

#### Automated Daily Backups (Cron Job)

Create backup script:

```bash
# Create script file
nano ~/scripts/backup-db.sh
```

**Script content:**
```bash
#!/bin/bash

# Configuration
DB_HOST="mysql.yourdomain.com"
DB_USER="yourusername_dbuser"
DB_PASS="your_password_here"
DB_NAME="yourusername_businessdb"
BACKUP_DIR="$HOME/backups/database"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Perform backup
mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete

echo "Backup completed: backup_$DATE.sql.gz"
```

Make executable:
```bash
chmod +x ~/scripts/backup-db.sh
```

**Set up cron job:**
```bash
crontab -e

# Add line (runs daily at 2 AM):
0 2 * * * /home/username/scripts/backup-db.sh >> /home/username/logs/backup.log 2>&1
```

#### Manual Backup

```bash
# Create backup
mysqldump -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb > ~/backups/manual-backup-$(date +%Y%m%d).sql

# Compress
gzip ~/backups/manual-backup-*.sql

# Download to local machine (from local terminal):
scp username@yourdomain.com:~/backups/manual-backup-*.sql.gz ~/Desktop/
```

### Application Updates

#### Update Laravel and Dependencies

```bash
cd ~/yourdomain.com

# Backup first!
mysqldump -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb > ~/backups/pre-update-$(date +%Y%m%d).sql

# Update Composer dependencies
composer update --no-dev

# Run any new migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild frontend assets (if package.json updated)
npm install
npm run build

# Test thoroughly before considering complete
```

#### Deploy Code Changes via Git

**Production Server Details:**
- **SSH Host:** `bentollenaar.dev` (configured in `~/.ssh/config`)
- **SSH User:** `btollenaar_dev`
- **Web Root:** `~/bentollenaar.dev/`
- **Git Repo:** `~/bentollenaar.dev/printstore/` (NOT the web root itself)
- **GitHub Remote:** `https://github.com/btollenaar/pod-storefront.git`

**⚠️ Important Nuances:**
1. The git repository is at `~/bentollenaar.dev/printstore/`, not the web root (`~/bentollenaar.dev/`)
2. GitHub HTTPS credentials are NOT cached on the server — `git pull origin main` will fail with "could not read Username"
3. Use a GitHub PAT token inline for pulls: `git pull https://<TOKEN>@github.com/btollenaar/pod-storefront.git main`
4. Frontend assets are pre-built locally and committed — no `npm run build` needed on server
5. The SSH connection shows a post-quantum key exchange warning — this is informational only and doesn't affect functionality

**Quick Deploy (Most Common — Code-Only Changes):**

```bash
# Get a GitHub token (from local machine)
gh auth token  # Copy this value

# SSH into DreamHost
ssh bentollenaar.dev
# (or: sshpass -p '<password>' ssh btollenaar_dev@bentollenaar.dev)

# Navigate to git repo (NOT web root)
cd ~/bentollenaar.dev/printstore

# Pull with token authentication
git pull https://<GITHUB_TOKEN>@github.com/btollenaar/pod-storefront.git main

# Clear caches (always do this)
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Full Deploy (Dependency or Database Changes):**

```bash
ssh bentollenaar.dev
cd ~/bentollenaar.dev/printstore

# Pull latest code (with token)
git pull https://<GITHUB_TOKEN>@github.com/btollenaar/pod-storefront.git main

# Install/update dependencies if composer.json changed
composer install --no-dev --optimize-autoloader

# Run migrations if database changes
php artisan migrate --force

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Automated Deploy via Claude Code:**

Claude Code can deploy directly using `sshpass` (installed via Homebrew):
```bash
# One-liner deployment from local machine
TOKEN=$(gh auth token) && sshpass -p '<password>' ssh btollenaar_dev@bentollenaar.dev \
  "cd ~/bentollenaar.dev/printstore && \
   git pull https://${TOKEN}@github.com/btollenaar/pod-storefront.git main && \
   php artisan config:clear && \
   php artisan cache:clear && \
   php artisan route:clear && \
   php artisan view:clear"
```

**When to Skip Steps:**
| Change Type | `composer install` | `migrate` | `npm run build` | Cache Clear |
|-------------|-------------------|-----------|-----------------|-------------|
| PHP only | No | No | No | ✅ Yes |
| Blade views | No | No | No | ✅ Yes |
| JS/CSS + build assets committed | No | No | No | ✅ Yes |
| New migration | No | ✅ Yes | No | ✅ Yes |
| composer.json changed | ✅ Yes | Maybe | No | ✅ Yes |
| Only CLAUDE.md/docs | No | No | No | Optional |

### Monitoring & Analytics

#### Laravel Log Monitoring

```bash
# View recent errors
tail -100 ~/yourdomain.com/storage/logs/laravel.log

# Watch logs in real-time
tail -f ~/yourdomain.com/storage/logs/laravel.log

# Search for specific errors
grep -i "error\|exception\|fatal" ~/yourdomain.com/storage/logs/laravel.log
```

#### Disk Space Monitoring

```bash
# Check disk usage
df -h ~

# Find large directories
du -sh ~/yourdomain.com/* | sort -h

# Clean up old logs (older than 30 days)
find ~/yourdomain.com/storage/logs -name "*.log" -mtime +30 -delete
```

#### Performance Monitoring

Consider integrating:
- **Laravel Telescope** (development only, resource-intensive)
- **New Relic** (paid, comprehensive monitoring)
- **Sentry** (error tracking and monitoring)
- **Google Analytics** (visitor tracking)

### Security Audits

#### Monthly Security Checklist

- [ ] Review Laravel security advisories: https://laravel-news.com/category/security
- [ ] Update Composer dependencies for security patches
- [ ] Review user accounts (disable inactive admin accounts)
- [ ] Check for unauthorized file changes:
  ```bash
  find ~/yourdomain.com -type f -name "*.php" -mtime -7
  ```
- [ ] Review Stripe Dashboard for unusual transactions
- [ ] Check failed login attempts in database
- [ ] Verify SSL certificate is valid and not expiring soon
- [ ] Review webhook logs for anomalies

---

## Newsletter Scheduler Setup (Optional)

If using the newsletter campaign feature, set up the Laravel scheduler:

### Enable Newsletter Scheduler

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler (runs every 5 minutes)
*/5 * * * * cd /home/username/yourdomain.com && php artisan schedule:run >> /dev/null 2>&1
```

This enables:
- Automatic sending of scheduled newsletter campaigns
- Runs every 5 minutes to check for pending campaigns
- Dispatches SendNewsletter job for campaigns with `scheduled_send_time <= now()`

**Test scheduler:**
```bash
# Run scheduler manually
php artisan schedule:run

# Check for scheduled newsletters
php artisan tinker
Newsletter::where('status', 'scheduled')->where('scheduled_send_time', '<=', now())->get();
exit
```

---

## Cost Breakdown

### DreamHost Hosting
- **Shared Hosting:** $10.95/month (promotional pricing may vary)
- **Domain Registration:** $15.99/year (if not already owned)
- **SSL Certificate:** Free (Let's Encrypt included)

### Third-Party Services
- **Stripe:** 2.9% + $0.30 per transaction (no monthly fee)
- **Email Service (Optional):**
  - DreamHost SMTP: Included with hosting
  - SendGrid: Free up to 100 emails/day, then $19.95/month for 50K emails
- **Domain Privacy (Optional):** $9/year

### Development & Maintenance
- **Initial Setup:** 2-3 hours (one-time)
- **Monthly Maintenance:** 1-2 hours (updates, backups, monitoring)

**Estimated Monthly Cost:** ~$11-30 (depending on email service choice)

---

## Support & Resources

### DreamHost Support
- **Panel:** https://panel.dreamhost.com
- **Help Center:** https://help.dreamhost.com
- **Contact Support:** support@dreamhost.com or via panel

### Laravel Resources
- **Documentation:** https://laravel.com/docs/11.x
- **Laracasts:** https://laracasts.com (video tutorials)
- **Laravel News:** https://laravel-news.com

### Stripe Resources
- **Dashboard:** https://dashboard.stripe.com
- **Documentation:** https://stripe.com/docs
- **Support:** support@stripe.com or via dashboard

### Project-Specific Documentation
- **README.md** - Installation, features, quick start
- **CLAUDE.md** - Architecture patterns, best practices, debugging tips
- **CUSTOMIZATION-GUIDE.md** - Business customization instructions
- **TEST-CREDENTIALS.md** - Test accounts and credentials
- **config/business.php** - Business configuration and feature toggles

---

## Appendix: Common Commands Reference

### Laravel Artisan Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches (production optimization)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database
php artisan migrate --force
php artisan db:seed --class=SeederName
php artisan tinker

# Storage
php artisan storage:link

# Queue (if using)
php artisan queue:work --daemon

# Show database info
php artisan db:show
php artisan db:table

# Newsletter scheduler (if enabled)
php artisan schedule:run
php artisan newsletters:send-scheduled
```

### Git Commands

```bash
# Pull latest changes
git pull origin main

# Check status
git status

# View recent commits
git log --oneline -10

# Stash local changes (if needed)
git stash
git pull origin main
git stash pop
```

### File Management

```bash
# Set permissions
chmod -R 775 storage bootstrap/cache
chmod 600 .env
chmod -R 700 .git

# Find files modified recently
find . -type f -mtime -7

# Check disk space
df -h ~
du -sh *
```

### Database Commands

```bash
# Connect to MySQL
mysql -h mysql.yourdomain.com -u yourusername_dbuser -p

# Backup database
mysqldump -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb > backup.sql

# Restore database
mysql -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb < backup.sql

# Show tables
mysql -h mysql.yourdomain.com -u yourusername_dbuser -p yourusername_businessdb -e "SHOW TABLES;"
```

---

## Conclusion

This deployment guide provides comprehensive instructions for deploying the **Laravel 11 Business Management Platform** to DreamHost shared hosting. By following these steps carefully, you'll have a production-ready application with:

- ✅ Secure SSL encryption
- ✅ Stripe payment processing
- ✅ Email notifications
- ✅ Database backups
- ✅ Optimized performance
- ✅ Security hardening
- ✅ Newsletter campaign system (optional)

**Next Steps After Deployment:**
1. Monitor error logs daily for the first week
2. Implement automated database backups (cron job)
3. Set up Google Analytics or similar tracking
4. Set up newsletter scheduler cron job (if using campaigns)
5. Gather customer feedback and iterate on features

**Questions or Issues?**
- Check the [Troubleshooting](#troubleshooting) section first
- Review Laravel logs: `storage/logs/laravel.log`
- Consult DreamHost support for hosting-specific issues
- Refer to `CLAUDE.md` for project-specific architecture notes

**Good luck with your launch!** 🚀
