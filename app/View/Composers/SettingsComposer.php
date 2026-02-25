<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class SettingsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Load contact settings with fallbacks to config values
        $contactSettings = [
            'email' => Setting::get('contact.email', config('business.contact.email')),
            'phone' => Setting::get('contact.phone', config('business.contact.phone')),
            'address' => [
                'street' => Setting::get('contact.address_street', config('business.contact.address.street')),
                'city' => Setting::get('contact.address_city', config('business.contact.address.city')),
                'state' => Setting::get('contact.address_state', config('business.contact.address.state')),
                'zip' => Setting::get('contact.address_zip', config('business.contact.address.zip')),
            ],
        ];

        // Load social media settings with fallbacks
        $socialSettings = [
            'facebook_url' => Setting::get('social.facebook_url', config('business.social_media.facebook_url')),
            'instagram_url' => Setting::get('social.instagram_url', config('business.social_media.instagram_url')),
            'twitter_url' => Setting::get('social.twitter_url', config('business.social_media.twitter_url')),
            'linkedin_url' => Setting::get('social.linkedin_url', config('business.social_media.linkedin_url')),
        ];

        // Load profile settings
        $profileSettings = [
            'name' => Setting::get('profile.business_name', config('business.profile.name')),
            'tagline' => Setting::get('profile.tagline', config('business.profile.tagline')),
        ];

        // Load homepage hero settings
        $homepageSettings = [
            'hero_business_name' => Setting::get('homepage.hero_business_name', config('business.profile.name', 'PrintStore')),
            'hero_headline' => Setting::get('homepage.hero_headline', config('business.profile.tagline', 'Custom merch, made on demand.')),
            'hero_description' => Setting::get('homepage.hero_description', 'Premium print-on-demand products designed for you. Browse our collection and get your order shipped directly to your door.'),
            'hero_products_button' => Setting::get('homepage.hero_products_button', 'Shop Now'),
            'hero_products_button_theme' => Setting::get('homepage.hero_products_button_theme', 'accent_color'),
        ];

        // Load feature settings
        $featureSettings = [
            'products' => (bool) Setting::get('features.products_enabled', config('business.features.products', true)),
            'donations' => (bool) Setting::get('features.donations_enabled', config('business.features.donations', false)),
            'events' => (bool) Setting::get('features.events_enabled', config('business.features.events', false)),
            'gallery' => (bool) Setting::get('features.gallery_enabled', config('business.features.gallery', false)),
            'fundraising_tracker' => (bool) Setting::get('features.fundraising_tracker_enabled', config('business.features.fundraising_tracker', false)),
            'sponsors' => (bool) Setting::get('features.sponsors_enabled', config('business.features.sponsors', false)),
            'cms_pages' => (bool) Setting::get('features.cms_pages_enabled', config('business.features.cms_pages', false)),
            'blog' => (bool) Setting::get('features.blog_enabled', config('business.features.blog', true)),
        ];

        // Share with view
        $view->with([
            'contactSettings' => $contactSettings,
            'socialSettings' => $socialSettings,
            'profileSettings' => $profileSettings,
            'homepageSettings' => $homepageSettings,
            'featureSettings' => $featureSettings,
        ]);
    }
}
