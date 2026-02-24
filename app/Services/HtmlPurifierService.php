<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlPurifierService
{
    protected HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();

        // Ensure cache directory exists
        $cachePath = storage_path('app/purifier');
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        // Set cache path
        $config->set('Cache.SerializerPath', $cachePath);

        // Allow safe HTML tags for blog/about content
        $config->set('HTML.Allowed', 'p,br,strong,em,u,a[href|title|target],ul,ol,li,h1,h2,h3,h4,h5,h6,blockquote,code,pre,img[src|alt|width|height],span[style]');

        // Allow safe CSS properties
        $config->set('CSS.AllowedProperties', 'color,background-color,font-weight,font-style,text-decoration,text-align');

        // Auto-paragraph
        $config->set('AutoFormat.AutoParagraph', true);

        // Ensure links open safely
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // Character encoding
        $config->set('Core.Encoding', 'UTF-8');

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Sanitize HTML content to prevent XSS attacks
     */
    public function clean(string $dirty): string
    {
        return $this->purifier->purify($dirty);
    }

    /**
     * Sanitize content and preserve newlines (for textarea content)
     */
    public function cleanWithNewlines(string $dirty): string
    {
        // Replace newlines with <br> tags before purifying
        $dirty = nl2br($dirty);
        return $this->purifier->purify($dirty);
    }
}
