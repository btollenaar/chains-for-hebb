<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use Auditable;

    protected $fillable = [
        'category',
        'key',
        'value',
        'type',
        'description',
        'order',
        'metadata',
    ];

    /**
     * Get a setting value by key (category.key format)
     *
     * @param string $key Format: 'category.key' (e.g., 'profile.business_name')
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Use cache to avoid repeated database queries
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            [$category, $settingKey] = explode('.', $key, 2);

            $setting = static::where('category', $category)
                ->where('key', $settingKey)
                ->first();

            if (!$setting) {
                return $default;
            }

            // Cast value based on type
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key Format: 'category.key'
     * @param mixed $value
     * @return Setting
     */
    public static function set(string $key, $value): Setting
    {
        [$category, $settingKey] = explode('.', $key, 2);

        $setting = static::updateOrCreate(
            ['category' => $category, 'key' => $settingKey],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Get all settings for a category
     *
     * @param string $category
     * @return \Illuminate\Support\Collection
     */
    public static function getByCategory(string $category)
    {
        return static::where('category', $category)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => static::castValue($setting->value, $setting->type)];
            });
    }

    /**
     * Cast value based on type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'image':
                // Support both storage paths AND manual public paths (backwards compatibility)
                if (!$value) {
                    return null;
                }
                if (str_starts_with($value, 'storage/')) {
                    return Storage::url($value);
                } elseif (str_starts_with($value, 'images/')) {
                    return '/' . $value; // Return relative path
                } elseif (str_starts_with($value, 'settings/')) {
                    return Storage::url($value); // New uploads
                }
                // Fallback: treat as public root file (e.g., favicon.ico)
                return '/' . $value;
            case 'color':
                return $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            default:
                return $value;
        }
    }

    /**
     * Set an image setting with file upload
     *
     * @param string $key Format: 'category.key'
     * @param UploadedFile $file
     * @param string|null $oldPath Old image path to delete
     * @return Setting
     */
    public static function setImage(string $key, UploadedFile $file, ?string $oldPath = null): Setting
    {
        // Delete old image if exists
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Store new image
        $path = $file->store('settings/branding', 'public');

        // Extract metadata
        $metadata = [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];

        // Get image dimensions if image file
        if (str_starts_with($file->getMimeType(), 'image/')) {
            [$width, $height] = getimagesize($file->getRealPath());
            $metadata['dimensions'] = "{$width}x{$height}";
        }

        [$category, $settingKey] = explode('.', $key, 2);

        $setting = static::updateOrCreate(
            ['category' => $category, 'key' => $settingKey],
            [
                'value' => $path,
                'type' => 'image',
                'metadata' => json_encode($metadata)
            ]
        );

        // Clear specific cache key
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Clear settings cache
     *
     * @param string|null $key Specific key to clear, or null to clear all settings
     */
    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            // Clear specific key
            Cache::forget("setting.{$key}");
        } else {
            // Clear all settings cache (better than flush which clears ALL cache)
            // Note: In a future optimization, we could use cache tags for even better granularity
            $categories = ['profile', 'contact', 'social', 'branding', 'features', 'hours', 'theme', 'seo', 'homepage', 'navigation'];
            foreach ($categories as $category) {
                $settings = static::where('category', $category)->get();
                foreach ($settings as $setting) {
                    Cache::forget("setting.{$category}.{$setting->key}");
                }
            }
        }
    }

    /**
     * Adjust the brightness of a hex color
     *
     * @param string $hex Hex color code (e.g., '#D77F48')
     * @param int $percent Percentage to adjust (-100 to 100, negative for darker)
     * @return string Adjusted hex color
     */
    public static function adjustBrightness(string $hex, int $percent): string
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust brightness
        $r = max(0, min(255, $r + ($r * $percent / 100)));
        $g = max(0, min(255, $g + ($g * $percent / 100)));
        $b = max(0, min(255, $b + ($b * $percent / 100)));

        // Convert back to hex
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }
}
