<?php

// config for PeterSowah/LaravelCashierRevenueCat
return [
    /*
    |--------------------------------------------------------------------------
    | RevenueCat API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your RevenueCat API settings. The API key and
    | project ID are required for the package to work.
    |
    */

    'api' => [
        'key' => config('services.revenuecat.key'),
        'project_id' => config('services.revenuecat.project_id'),
        'version' => config('services.revenuecat.version', 'v2'),
        'base_url' => config('services.revenuecat.base_url', 'https://api.revenuecat.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how webhooks from RevenueCat are handled. The webhook secret
    | is used to verify the authenticity of incoming webhooks.
    |
    */

    'webhook' => [
        'secret' => config('services.revenuecat.webhook_secret'),
        'tolerance' => config('services.revenuecat.webhook_tolerance', 300),
        'endpoint' => config('services.revenuecat.webhook_endpoint', 'webhook/revenuecat'),
        'allowed_ips' => config('services.revenuecat.webhook_allowed_ips', []),
        'rate_limit' => [
            'enabled' => config('services.revenuecat.webhook_rate_limit_enabled', true),
            'max_attempts' => config('services.revenuecat.webhook_rate_limit_attempts', 60),
            'decay_minutes' => config('services.revenuecat.webhook_rate_limit_decay', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for RevenueCat API responses. This can help reduce
    | API calls and improve performance.
    |
    */

    'cache' => [
        'enabled' => config('services.revenuecat.cache_enabled', true),
        'ttl' => config('services.revenuecat.cache_ttl', 3600),
        'prefix' => config('services.revenuecat.cache_prefix', 'revenuecat'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for RevenueCat API calls and webhook events.
    |
    */

    'logging' => [
        'enabled' => config('services.revenuecat.logging_enabled', true),
        'channel' => config('services.revenuecat.logging_channel', 'stack'),
        'level' => config('services.revenuecat.logging_level', 'debug'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how errors from the RevenueCat API are handled.
    |
    */

    'error_handling' => [
        'throw_exceptions' => config('services.revenuecat.throw_exceptions', true),
        'log_errors' => config('services.revenuecat.log_errors', true),
        'retry_on_error' => config('services.revenuecat.retry_on_error', true),
        'max_retries' => config('services.revenuecat.max_retries', 3),
    ],

    'currency' => config('services.revenuecat.currency', 'USD'),

    'model' => [
        'user' => config('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class),
    ],
];
