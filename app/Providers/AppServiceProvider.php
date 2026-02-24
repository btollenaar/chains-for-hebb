<?php

namespace App\Providers;

use App\Listeners\MigrateGuestCart;
use App\Listeners\MigrateGuestWishlist;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Policies\AddressPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Migrate guest cart and wishlist when user logs in or registers
        Event::listen([Login::class, Registered::class], MigrateGuestCart::class);
        Event::listen([Login::class, Registered::class], MigrateGuestWishlist::class);

        // Share cart count with header component
        View::composer('components.header', \App\View\Composers\CartComposer::class);

        // Share category navigation with header component
        View::composer('components.header', \App\View\Composers\CategoryComposer::class);

        // Share database settings with header, footer, mobile nav, home page, and about page
        View::composer(['components.header', 'components.footer', 'components.mobile-nav-drawer', 'home', 'about'], \App\View\Composers\SettingsComposer::class);

        // Share cart count with mobile nav drawer
        View::composer('components.mobile-nav-drawer', \App\View\Composers\CartComposer::class);

        // Share category navigation with mobile nav drawer
        View::composer('components.mobile-nav-drawer', \App\View\Composers\CategoryComposer::class);

        // Register authorization policies
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        // Register custom Blade directives for terminology
        $this->registerTerminologyDirectives();
    }

    /**
     * Register Blade directives for business terminology.
     *
     * Usage:
     *   @term('product.plural')  => "products"
     *   @Term('product.plural')  => "Products"
     *   @TERM('product.plural')  => "PRODUCTS"
     */
    protected function registerTerminologyDirectives(): void
    {
        // Lowercase terminology
        Blade::directive('term', function ($expression) {
            return "<?php echo config('business.terminology.' . {$expression}); ?>";
        });

        // Uppercase first letter
        Blade::directive('Term', function ($expression) {
            return "<?php echo ucfirst(config('business.terminology.' . {$expression})); ?>";
        });

        // All uppercase
        Blade::directive('TERM', function ($expression) {
            return "<?php echo strtoupper(config('business.terminology.' . {$expression})); ?>";
        });
    }
}
