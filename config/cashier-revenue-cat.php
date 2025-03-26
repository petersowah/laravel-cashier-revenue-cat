<?php

// config for PeterSowah/LaravelCashierRevenueCat
return [
    /*
    |--------------------------------------------------------------------------
    | RevenueCat API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your RevenueCat API settings. You'll need to add
    | your API keys to your .env file.
    |
    */

    'api' => [
        'version' => env('REVENUE_CAT_API_VERSION', 'v2'),
        'base_url' => env('REVENUE_CAT_API_BASE_URL', 'https://api.revenuecat.com'),
        'public_key' => env('REVENUE_CAT_PUBLIC_KEY'),
        'secret_key' => env('REVENUE_CAT_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RevenueCat Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your RevenueCat webhook settings. The webhook
    | secret should be added to your .env file.
    |
    */

    'webhook' => [
        'enabled' => env('REVENUE_CAT_WEBHOOK_ENABLED', true),
        'secret' => env('REVENUE_CAT_WEBHOOK_SECRET'),
        'url' => env('REVENUE_CAT_WEBHOOK_URL', '/revenue-cat/webhook'),
        'queue' => env('REVENUE_CAT_WEBHOOK_QUEUE', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RevenueCat Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure caching for RevenueCat API responses.
    |
    */

    'cache' => [
        'enabled' => env('REVENUE_CAT_CACHE_ENABLED', true),
        'ttl' => env('REVENUE_CAT_CACHE_TTL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | RevenueCat Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure logging for RevenueCat operations.
    |
    */

    'logging' => [
        'enabled' => env('REVENUE_CAT_LOG_ENABLED', true),
        'level' => env('REVENUE_CAT_LOG_LEVEL', 'debug'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RevenueCat Error Handling
    |--------------------------------------------------------------------------
    |
    | Here you can configure how errors are handled.
    |
    */

    'error_handling' => [
        'throw_on_error' => env('REVENUE_CAT_THROW_ON_ERROR', true),
        'retry_on_error' => env('REVENUE_CAT_RETRY_ON_ERROR', true),
        'max_retries' => env('REVENUE_CAT_MAX_RETRIES', 3),
    ],

    'currency' => config('services.revenuecat.currency', 'USD'),

    'model' => [
        'user' => config('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class),
    ],
];
