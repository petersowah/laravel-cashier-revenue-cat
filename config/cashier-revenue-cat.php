<?php

// config for PeterSowah/LaravelCashierRevenueCat
return [
    /*
    |--------------------------------------------------------------------------
    | RevenueCat API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your RevenueCat API credentials and settings.
    |
    */

    'api' => [
        'key' => env('REVENUECAT_API_KEY'),
        'project_id' => env('REVENUECAT_PROJECT_ID'),
        'version' => env('REVENUECAT_API_VERSION', 'v2'),
        'base_url' => env('REVENUECAT_API_BASE_URL', 'https://api.revenuecat.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your webhook settings.
    |
    */

    'webhook' => [
        'secret' => env('REVENUECAT_WEBHOOK_SECRET'),
        'tolerance' => env('REVENUECAT_WEBHOOK_TOLERANCE', 300),
        'endpoint' => env('REVENUECAT_WEBHOOK_ENDPOINT', 'webhook/revenuecat'),
        'route_group' => env('REVENUECAT_ROUTE_GROUP', 'web'),
        'route_middleware' => env('REVENUECAT_WEBHOOK_ROUTE_MIDDLEWARE', 'web'),
        'handler' => env('REVENUECAT_WEBHOOK_HANDLER', \PeterSowah\LaravelCashierRevenueCat\Http\Controllers\RevenueCatWebhookController::class),
        'allowed_ips' => env('REVENUECAT_WEBHOOK_ALLOWED_IPS', ''),
        'rate_limit' => [
            'enabled' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ENABLED', true),
            'max_attempts' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ATTEMPTS', 60),
            'decay_minutes' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_DECAY', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your database settings.
    |
    */

    'database' => [
        'customers_table' => env('REVENUECAT_DATABASE_CUSTOMERS_TABLE', 'customers'),
        'subscriptions_table' => env('REVENUECAT_DATABASE_SUBSCRIPTIONS_TABLE', 'subscriptions'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your cache settings.
    |
    */

    'cache' => [
        'enabled' => env('REVENUECAT_CACHE_ENABLED', true),
        'ttl' => env('REVENUECAT_CACHE_TTL', 3600),
        'prefix' => env('REVENUECAT_CACHE_PREFIX', 'revenuecat'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your logging settings.
    |
    */

    'logging' => [
        'enabled' => env('REVENUECAT_LOGGING_ENABLED', true),
        'channel' => env('REVENUECAT_LOGGING_CHANNEL', 'stack'),
        'level' => env('REVENUECAT_LOGGING_LEVEL', 'debug'),
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
        'throw_exceptions' => env('REVENUECAT_THROW_EXCEPTIONS', true),
        'log_errors' => env('REVENUECAT_LOG_ERRORS', true),
        'retry_on_error' => env('REVENUECAT_RETRY_ON_ERROR', true),
        'max_retries' => env('REVENUECAT_MAX_RETRIES', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Other Configuration
    |--------------------------------------------------------------------------
    |
    | Additional configuration options for the package.
    |
    */

    'currency' => env('REVENUECAT_CURRENCY', 'USD'),
    'model' => [
        'user' => env('REVENUECAT_USER_MODEL', \Illuminate\Foundation\Auth\User::class),
    ],
];
