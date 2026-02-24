<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fulfillment Providers
    |--------------------------------------------------------------------------
    */

    'printful' => [
        'api_key' => env('PRINTFUL_API_KEY'),
        'webhook_secret' => env('PRINTFUL_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics & Tracking
    |--------------------------------------------------------------------------
    */

    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'merchant_center_id' => env('GOOGLE_MERCHANT_CENTER_ID'),
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Calculation
    |--------------------------------------------------------------------------
    */

    'taxjar' => [
        'api_key' => env('TAXJAR_API_KEY'),
        'sandbox' => env('TAXJAR_SANDBOX', true),
    ],

];
