<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class BusinessConfig
{
    /**
     * Get cached business config with database-first, config-fallback strategy
     *
     * @param string $key Format: 'category.key' (e.g., 'profile.business_name', 'contact.email')
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = 'business_config_' . str_replace('.', '_', $key);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($key, $default) {
            // Try to get from database first
            $value = Setting::get($key, null);

            // If not in database, fall back to config file
            if ($value === null) {
                // Map our database keys to config keys
                // Database uses: profile.business_name, contact.email, etc.
                // Config uses: business.profile.name, business.contact.email, etc.
                $value = static::getFromConfig($key, $default);
            }

            return $value;
        });
    }

    /**
     * Get value from config file with key mapping
     *
     * @param string $key Database format key (e.g., 'profile.business_name')
     * @param mixed $default
     * @return mixed
     */
    protected static function getFromConfig(string $key, $default = null)
    {
        // Map database keys to config keys
        $keyMappings = [
            'profile.business_name' => 'business.profile.name',
            'profile.tagline' => 'business.profile.tagline',
            'profile.industry' => 'business.profile.industry',
            'contact.email' => 'business.contact.email',
            'contact.phone' => 'business.contact.phone',
            'contact.address_street' => 'business.contact.address.street',
            'contact.address_city' => 'business.contact.address.city',
            'contact.address_state' => 'business.contact.address.state',
            'contact.address_zip' => 'business.contact.address.zip',
            'social.facebook_url' => 'business.social_media.facebook_url',
            'social.instagram_url' => 'business.social_media.instagram_url',
            'social.twitter_url' => 'business.social_media.twitter_url',
            'social.linkedin_url' => 'business.social_media.linkedin_url',
            'social.google_maps_url' => 'business.social_media.google_maps_url',
            'branding.logo_path' => 'business.branding.logo_path',
            'branding.logo_alt' => 'business.branding.logo_alt',
            'branding.favicon_path' => 'business.branding.favicon_path',
            'branding.hero_video_url' => 'business.branding.hero_video_url',
            'features.products_enabled' => 'business.features.products',
            'features.services_enabled' => 'business.features.services',
            'features.appointments_enabled' => 'business.features.appointments',
            'features.blog_enabled' => 'business.features.blog',
            'features.reviews_enabled' => 'business.features.reviews',
            'features.gift_cards_enabled' => 'business.features.gift_cards',
        ];

        $configKey = $keyMappings[$key] ?? 'business.' . $key;
        return config($configKey, $default);
    }

    /**
     * Get contact information (returns array with all contact fields)
     */
    public static function contact(): array
    {
        return Cache::remember('business_config_contact', now()->addDay(), function () {
            // Try to get from database first
            $email = Setting::get('contact.email', null);

            if ($email !== null) {
                // Database has contact info
                return [
                    'email' => Setting::get('contact.email'),
                    'phone' => Setting::get('contact.phone'),
                    'address' => [
                        'street' => Setting::get('contact.address_street'),
                        'city' => Setting::get('contact.address_city'),
                        'state' => Setting::get('contact.address_state'),
                        'zip' => Setting::get('contact.address_zip'),
                    ],
                ];
            }

            // Fallback to config
            return config('business.contact');
        });
    }

    /**
     * Get business hours (returns array with all days)
     */
    public static function hours(): array
    {
        return Cache::remember('business_config_hours', now()->addDay(), function () {
            // Try to get from database first
            $monday = Setting::get('hours.monday', null);

            if ($monday !== null) {
                // Database has hours
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $hours = [];

                foreach ($days as $day) {
                    $hours[$day] = Setting::get("hours.{$day}");
                }

                return $hours;
            }

            // Fallback to config
            return config('business.hours');
        });
    }

    /**
     * Get full address string
     */
    public static function addressString(): string
    {
        return Cache::remember('business_config_address_string', now()->addDay(), function () {
            $contact = static::contact();
            $address = $contact['address'];
            return $address['street'] . ', ' . $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'];
        });
    }

    /**
     * Clear all business config cache
     */
    public static function clearCache(): void
    {
        Cache::flush(); // Clear all cache to ensure settings are refreshed
    }
}
