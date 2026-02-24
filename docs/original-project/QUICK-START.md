# Quick Start Guide

Get your business storefront running in minutes!

## Prerequisites Checklist

- [ ] PHP 8.1+ installed
- [ ] Composer installed
- [ ] MySQL/MariaDB running
- [ ] Node.js 18+ and npm installed
- [ ] Git (optional, for version control)

## Installation Steps

### 1. Install Laravel

```bash
# Create new Laravel project
composer create-project laravel/laravel your-business-name

# Navigate to project
cd your-business-name

# Install dependencies
composer require laravel/breeze --dev
composer require stripe/stripe-php
composer require intervention/image
```

### 2. Copy Platform Files

Copy these files from the platform template to your new Laravel project:

```bash
# Database migrations
cp database/migrations/* your-business-name/database/migrations/

# Models
cp -r app/Models/* your-business-name/app/Models/

# Controllers
cp -r app/Http/Controllers/* your-business-name/app/Http/Controllers/

# Configuration
cp config-business.php your-business-name/config/business.php

# Documentation
cp -r docs/ your-business-name/docs/
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your business details:

```env
APP_NAME="Your Business Name"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Business Settings
BUSINESS_TYPE=wellness
BUSINESS_NAME="Your Business Name"
BUSINESS_EMAIL=info@yourbusiness.com
BUSINESS_PHONE=123-456-7890

# Features (true/false)
FEATURE_PRODUCTS=true
FEATURE_SERVICES=true
FEATURE_APPOINTMENTS=true
FEATURE_GIFT_CARDS=true
FEATURE_BLOG=true
FEATURE_REVIEWS=true

# Payment
PAYMENT_CURRENCY=USD
TAX_RATE=0.07
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### 4. Set Up Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Optional: Seed sample data
php artisan db:seed
```

### 5. Install Authentication

```bash
# Install Laravel Breeze
php artisan breeze:install blade

# Install frontend dependencies
npm install

# Build assets
npm run dev
```

### 6. Set Up Storage

```bash
# Create symbolic link for public storage
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### 7. Run the Application

```bash
# Start development server
php artisan serve

# In another terminal, watch for asset changes
npm run dev
```

Visit: **http://localhost:8000**

---

## Business Type Quick Configs

### Wellness / Medical Aesthetics

```env
BUSINESS_TYPE=wellness

FEATURE_PRODUCTS=true
FEATURE_SERVICES=true
FEATURE_APPOINTMENTS=true
FEATURE_GIFT_CARDS=true
FEATURE_REVIEWS=true
```

### Hair Salon / Spa

```env
BUSINESS_TYPE=salon

FEATURE_PRODUCTS=true
FEATURE_SERVICES=true
FEATURE_APPOINTMENTS=true
FEATURE_GIFT_CARDS=true
FEATURE_MEMBERSHIPS=false
```

### Professional Services

```env
BUSINESS_TYPE=professional

FEATURE_PRODUCTS=false
FEATURE_SERVICES=true
FEATURE_APPOINTMENTS=true
FEATURE_REVIEWS=true
```

### Retail + Services

```env
BUSINESS_TYPE=hybrid

FEATURE_PRODUCTS=true
FEATURE_SERVICES=true
FEATURE_APPOINTMENTS=true
FEATURE_LOYALTY_PROGRAM=true
```

---

## Common Commands

### Development

```bash
# Run development server
php artisan serve

# Watch and compile frontend assets
npm run dev

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database and re-run migrations
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

### Creating New Resources

```bash
# Create model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller ControllerName

# Create seeder
php artisan make:seeder SeederName
```

---

## Next Steps

### 1. Add Your Services

Create a seeder or use the admin panel (once built):

```php
// database/seeders/YourBusinessSeeder.php
Service::create([
    'name' => 'IV Vitamin Therapy',
    'slug' => 'iv-vitamin-therapy',
    'category' => 'iv_therapy',
    'price' => 150.00,
    'duration_minutes' => 60,
    'description' => 'Boost your immune system...',
    'is_active' => true,
]);
```

Run: `php artisan db:seed --class=YourBusinessSeeder`

### 2. Add Your Products

```php
Product::create([
    'name' => 'Vitamin C Serum',
    'sku' => 'VCS-001',
    'category' => 'skincare',
    'price' => 45.00,
    'stock_quantity' => 50,
    'is_active' => true,
]);
```

### 3. Configure Payment Gateway

Sign up for Stripe (https://stripe.com) and add keys to `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 4. Customize Theme

- Add logo to `public/images/logo.png`
- Edit colors in `resources/css/custom.css`
- Customize email templates in `resources/views/emails/`

### 5. Build Admin Panel

Create admin views and controllers:

```bash
# Create admin dashboard view
mkdir -p resources/views/admin
```

Use the sample [DashboardController.php](app/Http/Controllers/Admin/DashboardController.php) as reference.

---

## Troubleshooting

### Database Connection Issues

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

If fails:
- Check credentials in `.env`
- Ensure MySQL is running
- Verify database exists

### Permission Errors

```bash
# Linux/Mac - fix permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

### Assets Not Loading

```bash
# Rebuild assets
npm run dev

# Check public/storage link exists
php artisan storage:link
```

### Migration Errors

```bash
# Drop all tables and re-migrate
php artisan migrate:fresh

# If foreign key errors, check migration order
# Migrations should be numbered sequentially
```

---

## Production Deployment

### Before Going Live

1. **Set environment to production**:
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize performance**:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set up SSL certificate** (Let's Encrypt recommended)

4. **Configure email service** (SendGrid, Mailgun, etc.)

5. **Set up backups**:
   - Database backups (daily)
   - File backups (weekly)

6. **Enable queue workers**:
```bash
php artisan queue:work --daemon
```

7. **Set up monitoring**:
   - Error tracking (Sentry, Bugsnag)
   - Uptime monitoring
   - Performance monitoring

---

## Getting Help

- **Laravel Documentation**: https://laravel.com/docs
- **Platform Docs**: See `docs/` folder
  - [ARCHITECTURE.md](docs/ARCHITECTURE.md) - System design
  - [CUSTOMIZATION.md](docs/CUSTOMIZATION.md) - Business customization
- **Laravel Community**: https://laracasts.com/discuss

---

## File Structure Reference

```
your-business-name/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              # Admin panel
│   │   └── Store/              # Customer-facing
│   ├── Models/                 # Database models
│   └── Services/               # Business logic
├── config/
│   └── business.php            # Business configuration
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Sample data
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin templates
│   │   ├── store/              # Storefront templates
│   │   └── emails/             # Email templates
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
├── public/                     # Public assets
├── storage/                    # File storage
└── docs/                       # Documentation
```

---

## Success Checklist

After setup, you should be able to:

- [ ] Access homepage at http://localhost:8000
- [ ] Browse products (if enabled)
- [ ] Browse services (if enabled)
- [ ] Add items to cart
- [ ] Register as customer
- [ ] Book an appointment
- [ ] Admin login
- [ ] View admin dashboard
- [ ] Manage orders
- [ ] Manage appointments

**Congratulations!** Your business platform is ready to customize and launch! 🎉
