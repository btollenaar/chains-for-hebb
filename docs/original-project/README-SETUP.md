# Reusable Business Storefront Platform

A modular Laravel-based platform for service-based businesses with e-commerce capabilities.

## Prerequisites

Before installation, ensure you have:
- PHP 8.1 or higher
- Composer (https://getcomposer.org/)
- MySQL 8.0 or MariaDB 10.3+
- Node.js 18+ and npm (for frontend assets)

## Installation Steps

### 1. Install Laravel

```bash
# Create a new Laravel project
composer create-project laravel/laravel wellness-platform
cd wellness-platform

# Install additional dependencies
composer require laravel/breeze --dev
composer require stripe/stripe-php
composer require intervention/image
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wellness_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Set Up Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE wellness_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations (after copying migration files)
php artisan migrate

# Seed initial data
php artisan db:seed
```

### 4. Install Frontend Dependencies

```bash
# Install Breeze for authentication scaffolding
php artisan breeze:install blade
npm install
npm run dev
```

### 5. Set Up Storage

```bash
# Create symbolic link for public storage
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### 6. Run the Application

```bash
# Development server
php artisan serve

# Access at: http://localhost:8000
```

## Project Architecture

This platform uses a **modular approach** to support multiple business types:

### Core Modules
- **Customer Management** - Universal customer/user system
- **Product Catalog** - Physical and digital products
- **Service Booking** - Appointment scheduling
- **Order Processing** - Cart, checkout, order management
- **Payment Integration** - Stripe, PayPal support
- **Admin Dashboard** - Business management interface

### Business Customization

Each business type can be configured via:

1. **config/business.php** - Business-specific settings
2. **Business Profiles** - Database-driven configurations
3. **Theme System** - Custom branding and layouts
4. **Module Toggle** - Enable/disable features per business

### Supported Business Types (Out of Box)

- Wellness/Medical Aesthetics
- Professional Services
- Retail + Services hybrid
- Subscription-based services

## Customizing for a New Business

See `docs/CUSTOMIZATION.md` for detailed instructions on:
- Configuring business profiles
- Adding custom service types
- Theming and branding
- Module configuration

## Directory Structure

```
wellness-platform/
├── app/
│   ├── Models/           # Eloquent models
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/    # Admin panel controllers
│   │   │   ├── Store/    # Storefront controllers
│   │   │   └── Api/      # API endpoints
│   ├── Services/         # Business logic services
│   └── Modules/          # Modular features
├── database/
│   ├── migrations/       # Database schema
│   └── seeders/          # Sample data
├── resources/
│   ├── views/
│   │   ├── admin/        # Admin templates
│   │   ├── store/        # Storefront templates
│   │   └── layouts/      # Shared layouts
│   └── js/               # Frontend JavaScript
├── routes/
│   ├── web.php           # Web routes
│   ├── admin.php         # Admin routes
│   └── api.php           # API routes
├── config/
│   └── business.php      # Business configuration
└── docs/                 # Documentation
```

## Next Steps

1. Review the database migrations in `database/migrations/`
2. Customize `config/business.php` for your business
3. Update branding in `resources/views/layouts/`
4. Configure payment gateway credentials
5. Set up email service (Mailgun, SendGrid, etc.)

## Security Checklist

- [ ] Change default APP_KEY
- [ ] Set secure passwords for database
- [ ] Configure CSRF protection
- [ ] Set up SSL certificate (production)
- [ ] Configure rate limiting
- [ ] Review file upload restrictions
- [ ] Enable database backups

## Support

For issues or questions about this platform architecture, refer to:
- Laravel Documentation: https://laravel.com/docs
- Project Wiki: (to be created)
