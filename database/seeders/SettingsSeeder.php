<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Profile Settings
            [
                'category' => 'profile',
                'key' => 'business_name',
                'value' => config('business.profile.name'),
                'type' => 'string',
                'description' => 'Your business name displayed throughout the site',
                'order' => 1,
            ],
            [
                'category' => 'profile',
                'key' => 'tagline',
                'value' => config('business.profile.tagline'),
                'type' => 'string',
                'description' => 'A short tagline or slogan for your business',
                'order' => 2,
            ],
            // Contact Settings
            [
                'category' => 'contact',
                'key' => 'email',
                'value' => config('business.contact.email'),
                'type' => 'string',
                'description' => 'Primary business email address',
                'order' => 1,
            ],
            [
                'category' => 'contact',
                'key' => 'phone',
                'value' => config('business.contact.phone'),
                'type' => 'string',
                'description' => 'Primary business phone number',
                'order' => 2,
            ],
            [
                'category' => 'contact',
                'key' => 'address_street',
                'value' => config('business.contact.address.street'),
                'type' => 'string',
                'description' => 'Street address',
                'order' => 3,
            ],
            [
                'category' => 'contact',
                'key' => 'address_city',
                'value' => config('business.contact.address.city'),
                'type' => 'string',
                'description' => 'City',
                'order' => 4,
            ],
            [
                'category' => 'contact',
                'key' => 'address_state',
                'value' => config('business.contact.address.state'),
                'type' => 'string',
                'description' => 'State/Province',
                'order' => 5,
            ],
            [
                'category' => 'contact',
                'key' => 'address_zip',
                'value' => config('business.contact.address.zip'),
                'type' => 'string',
                'description' => 'ZIP/Postal Code',
                'order' => 6,
            ],

            // Social Media Settings
            [
                'category' => 'social',
                'key' => 'facebook_url',
                'value' => config('business.social_media.facebook_url'),
                'type' => 'url',
                'description' => 'Facebook page URL (leave empty to hide)',
                'order' => 1,
            ],
            [
                'category' => 'social',
                'key' => 'instagram_url',
                'value' => config('business.social_media.instagram_url'),
                'type' => 'url',
                'description' => 'Instagram profile URL (leave empty to hide)',
                'order' => 2,
            ],
            [
                'category' => 'social',
                'key' => 'twitter_url',
                'value' => config('business.social_media.twitter_url'),
                'type' => 'url',
                'description' => 'Twitter/X profile URL (leave empty to hide)',
                'order' => 3,
            ],
            [
                'category' => 'social',
                'key' => 'linkedin_url',
                'value' => config('business.social_media.linkedin_url'),
                'type' => 'url',
                'description' => 'LinkedIn company page URL (leave empty to hide)',
                'order' => 4,
            ],
            // Branding Settings
            [
                'category' => 'branding',
                'key' => 'logo_path',
                'value' => config('business.branding.logo_path'),
                'type' => 'image',
                'description' => 'Path to your logo image (relative to public folder)',
                'order' => 1,
            ],
            [
                'category' => 'branding',
                'key' => 'logo_alt',
                'value' => config('business.branding.logo_alt'),
                'type' => 'string',
                'description' => 'Alt text for logo (for accessibility)',
                'order' => 2,
            ],
            [
                'category' => 'branding',
                'key' => 'favicon_path',
                'value' => 'favicon.ico',
                'type' => 'image',
                'description' => 'Path to favicon (relative to public folder)',
                'order' => 3,
            ],
            [
                'category' => 'branding',
                'key' => 'hero_video_url',
                'value' => config('business.branding.hero_video_url'),
                'type' => 'url',
                'description' => 'Vimeo or YouTube URL for homepage hero video',
                'order' => 4,
            ],

            // Feature Toggles
            [
                'category' => 'features',
                'key' => 'products_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable product sales (e-commerce)',
                'order' => 1,
            ],
            [
                'category' => 'features',
                'key' => 'blog_enabled',
                'value' => config('business.features.blog') ? '1' : '0',
                'type' => 'boolean',
                'description' => 'Enable blog/content management',
                'order' => 2,
            ],
            [
                'category' => 'features',
                'key' => 'reviews_enabled',
                'value' => config('business.features.reviews') ? '1' : '0',
                'type' => 'boolean',
                'description' => 'Enable customer reviews',
                'order' => 3,
            ],

            // Theme Settings
            [
                'category' => 'theme',
                'key' => 'primary_color',
                'value' => '#0A0A0A',
                'type' => 'color',
                'description' => 'Primary brand color (near-black)',
                'order' => 1,
            ],
            [
                'category' => 'theme',
                'key' => 'secondary_color',
                'value' => '#6B7280',
                'type' => 'color',
                'description' => 'Secondary brand color (neutral gray)',
                'order' => 2,
            ],
            [
                'category' => 'theme',
                'key' => 'accent_color',
                'value' => '#FF3366',
                'type' => 'color',
                'description' => 'Accent color for CTAs and highlights (bold pink-red)',
                'order' => 3,
            ],
            [
                'category' => 'theme',
                'key' => 'admin_color',
                'value' => '#2D6069',
                'type' => 'color',
                'description' => 'Admin interface color (teal)',
                'order' => 4,
            ],
            [
                'category' => 'theme',
                'key' => 'background_color',
                'value' => '#FAFAFA',
                'type' => 'color',
                'description' => 'Background color (near-white)',
                'order' => 5,
            ],

            // Homepage Hero Settings
            [
                'category' => 'homepage',
                'key' => 'hero_business_name',
                'value' => 'PrintStore',
                'type' => 'string',
                'description' => 'Business name shown in hero section',
                'order' => 1,
            ],
            [
                'category' => 'homepage',
                'key' => 'hero_headline',
                'value' => 'Custom merch, made on demand.',
                'type' => 'string',
                'description' => 'Main headline in hero section',
                'order' => 2,
            ],
            [
                'category' => 'homepage',
                'key' => 'hero_description',
                'value' => 'Premium print-on-demand products designed for you. Browse our collection and get your order shipped directly to your door.',
                'type' => 'text',
                'description' => 'Description text below headline',
                'order' => 3,
            ],
            [
                'category' => 'homepage',
                'key' => 'hero_products_button',
                'value' => 'Shop Now',
                'type' => 'string',
                'description' => 'Products button text',
                'order' => 4,
            ],
            [
                'category' => 'homepage',
                'key' => 'hero_products_button_theme',
                'value' => 'accent_color',
                'type' => 'string',
                'description' => 'Theme color for products button',
                'order' => 5,
            ],

            // Navigation Settings
            [
                'category' => 'navigation',
                'key' => 'nav_products_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Show Products link in navigation',
                'order' => 1,
            ],
            [
                'category' => 'navigation',
                'key' => 'nav_products_url',
                'value' => '/products',
                'type' => 'string',
                'description' => 'URL for Products navigation link',
                'order' => 2,
            ],

            // Note: Shipping rates are fetched from Printful API, not stored locally
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                [
                    'category' => $setting['category'],
                    'key' => $setting['key'],
                ],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
