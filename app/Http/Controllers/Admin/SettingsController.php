<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Display the settings management interface
     */
    public function index()
    {
        // Get all settings in a single query, grouped by category
        $settingsGrouped = Setting::orderBy('category')->orderBy('order')->get()->groupBy('category');

        $profileSettings = $settingsGrouped->get('profile', collect())->reject(fn ($s) => $s->key === 'industry');
        $contactSettings = $settingsGrouped->get('contact', collect());
        $socialSettings = $settingsGrouped->get('social', collect())->reject(fn ($s) => $s->key === 'google_maps_url');
        $brandingSettings = $settingsGrouped->get('branding', collect());
        $featureSettings = $settingsGrouped->get('features', collect());
        $themeSettings = $settingsGrouped->get('theme', collect());
        $homepageSettings = $settingsGrouped->get('homepage', collect());
        $navigationSettings = $settingsGrouped->get('navigation', collect());

        // Also pass all settings for forms that need to query multiple categories
        $settings = Setting::all();

        return view('admin.settings.index', compact(
            'settings',
            'profileSettings',
            'contactSettings',
            'socialSettings',
            'brandingSettings',
            'featureSettings',
            'themeSettings',
            'homepageSettings',
            'navigationSettings'
        ));
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set("profile.{$key}", $value);
        }

        Setting::clearCache();

        return back()->with('success', 'Profile settings updated successfully.');
    }

    /**
     * Update contact settings
     */
    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\(\)\+\.]+$/'],
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:100',
            'address_state' => 'required|string|max:50',
            'address_zip' => 'required|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set("contact.{$key}", $value);
        }

        Setting::clearCache();

        return back()->with('success', 'Contact information updated successfully.');
    }

    /**
     * Update social media settings
     */
    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set("social.{$key}", $value);
        }

        Setting::clearCache();

        return back()->with('success', 'Social media links updated successfully.');
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'logo_alt' => 'nullable|string|max:255',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,ico|max:1024', // 1MB
            'hero_video_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be|vimeo\.com|player\.vimeo\.com|wistia\.com)\/.*/i'
            ],
        ], [
            'hero_video_url.regex' => 'The video URL must be from YouTube, Vimeo, or Wistia.',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Get old path for deletion
            $oldLogoPath = Setting::where('category', 'branding')
                ->where('key', 'logo_path')
                ->first()?->value;

            Setting::setImage('branding.logo_path', $request->file('logo'), $oldLogoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Get old path for deletion
            $oldFaviconPath = Setting::where('category', 'branding')
                ->where('key', 'favicon_path')
                ->first()?->value;

            Setting::setImage('branding.favicon_path', $request->file('favicon'), $oldFaviconPath);
        }

        // Handle text fields
        if (isset($validated['logo_alt'])) {
            Setting::set('branding.logo_alt', $validated['logo_alt']);
        }

        if (isset($validated['hero_video_url'])) {
            Setting::set('branding.hero_video_url', $validated['hero_video_url']);
        }

        return back()->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Update feature toggles
     */
    public function updateFeatures(Request $request)
    {
        $validated = $request->validate([
            'products_enabled' => 'nullable|boolean',
            'blog_enabled' => 'nullable|boolean',
            'reviews_enabled' => 'nullable|boolean',
        ]);

        // Convert checkbox values (checkboxes not present = false)
        $features = [
            'products_enabled',
            'blog_enabled',
            'reviews_enabled',
        ];

        foreach ($features as $feature) {
            $value = isset($validated[$feature]) && $validated[$feature] ? '1' : '0';
            Setting::set("features.{$feature}", $value);
        }

        Setting::clearCache();

        return back()->with('success', 'Feature settings updated successfully.');
    }

    /**
     * Update theme settings
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'admin_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set("theme.{$key}", $value);
        }

        // Clear view cache to regenerate with new colors
        Artisan::call('view:clear');

        return back()->with('success', 'Theme colors updated successfully. Refresh the page to see changes.');
    }

    /**
     * Update homepage hero settings and navigation
     */
    public function updateHomepage(Request $request)
    {
        $validated = $request->validate([
            'hero_business_name' => 'required|string|max:255',
            'hero_headline' => 'required|string|max:255',
            'hero_description' => 'required|string|max:500',
            'hero_products_button' => 'required|string|max:50',
            'hero_products_button_theme' => 'required|string|in:primary_color,secondary_color,accent_color,admin_color,background_color',
            'nav_products_enabled' => 'nullable|boolean',
            'nav_products_url' => 'required|string|max:255',
        ]);

        // Handle homepage settings including button themes
        foreach (['hero_business_name', 'hero_headline', 'hero_description', 'hero_products_button', 'hero_products_button_theme'] as $key) {
            Setting::set("homepage.{$key}", $validated[$key]);
        }

        // Handle navigation checkboxes (unchecked = not in request)
        $validated['nav_products_enabled'] = $request->has('nav_products_enabled') ? '1' : '0';

        // Save navigation settings
        foreach (['nav_products_enabled', 'nav_products_url'] as $key) {
            Setting::set("navigation.{$key}", $validated[$key]);
        }

        Setting::clearCache();

        return back()->with('success', 'Homepage settings updated successfully.');
    }

    public function updateNavigation(Request $request)
    {
        $validated = $request->validate([
            'nav_products_url' => 'required|string|max:255',
        ]);

        $validated['nav_products_enabled'] = $request->has('nav_products_enabled') ? '1' : '0';

        foreach (['nav_products_enabled', 'nav_products_url'] as $key) {
            Setting::set("navigation.{$key}", $validated[$key]);
        }

        Setting::clearCache();

        return back()->with('success', 'Navigation settings updated successfully.');
    }
}
