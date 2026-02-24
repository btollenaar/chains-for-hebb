<?php

/**
 * Business Configuration
 *
 * Chains for Hebb — Disc Golf Fundraiser Store
 * Raising $15,000 to build a disc golf course at Hebb County Park, West Linn, OR
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Business Profile
    |--------------------------------------------------------------------------
    */

    'profile' => [
        'type' => env('BUSINESS_TYPE', 'fundraiser'),
        'name' => env('BUSINESS_NAME', 'Chains for Hebb'),
        'tagline' => env('BUSINESS_TAGLINE', 'Help us build a disc golf course at Hebb County Park'),
        'industry' => env('BUSINESS_INDUSTRY', 'nonprofit_fundraiser'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Terminology Configuration
    |--------------------------------------------------------------------------
    */

    'terminology' => [
        'product' => [
            'singular' => env('TERM_PRODUCT_SINGULAR', 'Product'),
            'plural' => env('TERM_PRODUCT_PLURAL', 'Products'),
        ],
        'customer' => [
            'singular' => env('TERM_CUSTOMER_SINGULAR', 'Supporter'),
            'plural' => env('TERM_CUSTOMER_PLURAL', 'Supporters'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding Assets
    |--------------------------------------------------------------------------
    */

    'branding' => [
        'logo_path' => env('BRANDING_LOGO_PATH', 'images/logo.png'),
        'logo_alt' => env('BRANDING_LOGO_ALT', 'Chains for Hebb Logo'),
        'favicon_path' => env('BRANDING_FAVICON', 'favicon.ico'),
        'hero_video_url' => env('BRANDING_HERO_VIDEO', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Links
    |--------------------------------------------------------------------------
    */

    'social_media' => [
        'facebook_url' => env('SOCIAL_FACEBOOK', null),
        'instagram_url' => env('SOCIAL_INSTAGRAM', null),
        'twitter_url' => env('SOCIAL_TWITTER', null),
        'linkedin_url' => env('SOCIAL_LINKEDIN', null),
        'google_maps_url' => env('SOCIAL_GOOGLE_MAPS', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    */

    'theme' => [
        'primary_color' => env('THEME_PRIMARY_COLOR', '#2D5016'),
        'secondary_color' => env('THEME_SECONDARY_COLOR', '#8B6914'),
        'accent_color' => env('THEME_ACCENT_COLOR', '#E85D04'),
        'primary_font' => env('THEME_PRIMARY_FONT', 'Inter'),
        'heading_font' => env('THEME_HEADING_FONT', 'Oswald'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage Content
    |--------------------------------------------------------------------------
    */

    'homepage' => [
        'hero_heading' => env('HOME_HERO_HEADING', 'Bring Disc Golf to Hebb Park'),
        'hero_subheading' => env('HOME_HERO_SUBHEADING', 'Help us raise $15,000 to build an 18-hole disc golf course at one of West Linn\'s most beautiful parks.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Modules
    |--------------------------------------------------------------------------
    */

    'features' => [
        'products' => env('FEATURE_PRODUCTS', true),
        'donations' => env('FEATURE_DONATIONS', true),
        'events' => env('FEATURE_EVENTS', true),
        'sponsors' => env('FEATURE_SPONSORS', true),
        'cms_pages' => env('FEATURE_CMS_PAGES', true),
        'gallery' => env('FEATURE_GALLERY', true),
        'fundraising_tracker' => env('FEATURE_FUNDRAISING_TRACKER', true),
        'blog' => env('FEATURE_BLOG', true),
        'reviews' => env('FEATURE_REVIEWS', true),
        'services' => false,
        'appointments' => false,
        'memberships' => false,
        'gift_cards' => false,
        'loyalty_program' => false,
        'multi_location' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Settings
    |--------------------------------------------------------------------------
    */

    'products' => [
        'inventory_tracking' => false,
        'allow_backorder' => true,
        'low_stock_threshold' => 0,
        'fulfillment_type' => 'printful',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */

    'payments' => [
        'enabled_methods' => [
            'stripe' => true,
            'paypal' => false,
            'cash' => false,
            'check' => false,
        ],
        'currency' => env('PAYMENT_CURRENCY', 'USD'),
        'tax_rate' => env('TAX_RATE', 0.0),
        'require_deposit' => false,
        'deposit_percentage' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('BUSINESS_EMAIL', 'hello@chainsforhebb.org'),
        'phone' => env('BUSINESS_PHONE', ''),
        'address' => [
            'street' => env('BUSINESS_ADDRESS_STREET', ''),
            'city' => env('BUSINESS_ADDRESS_CITY', 'West Linn'),
            'state' => env('BUSINESS_ADDRESS_STATE', 'OR'),
            'zip' => env('BUSINESS_ADDRESS_ZIP', '97068'),
            'country' => env('BUSINESS_ADDRESS_COUNTRY', 'USA'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Operating Hours (N/A for fundraiser — always online)
    |--------------------------------------------------------------------------
    */

    'hours' => [
        'monday' => ['open' => '00:00', 'close' => '23:59'],
        'tuesday' => ['open' => '00:00', 'close' => '23:59'],
        'wednesday' => ['open' => '00:00', 'close' => '23:59'],
        'thursday' => ['open' => '00:00', 'close' => '23:59'],
        'friday' => ['open' => '00:00', 'close' => '23:59'],
        'saturday' => ['open' => '00:00', 'close' => '23:59'],
        'sunday' => ['open' => '00:00', 'close' => '23:59'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'order_confirmation' => true,
        'shipping_updates' => true,
        'admin_new_order' => true,
        'donation_received' => true,
        'event_rsvp' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Printful Integration
    |--------------------------------------------------------------------------
    */

    'printful' => [
        'api_key' => env('PRINTFUL_API_KEY'),
        'store_id' => env('PRINTFUL_STORE_ID'),
        'webhook_secret' => env('PRINTFUL_WEBHOOK_SECRET'),
        'catalog_cache_hours' => 24,
        'shipping_cache_minutes' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fundraising Configuration
    |--------------------------------------------------------------------------
    */

    'fundraising' => [
        'goal_amount' => env('FUNDRAISING_GOAL', 15000.00),
        'organization_name' => env('ORG_NAME', 'Chains for Hebb'),
        'tax_deductible' => env('DONATIONS_TAX_DEDUCTIBLE', true),
    ],

];
