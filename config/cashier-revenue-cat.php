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
        'key' => config('app.revenuecat.api.key'),
        'project_id' => config('app.revenuecat.api.project_id'),
        'version' => config('app.revenuecat.api.version', 'v2'),
        'base_url' => config('app.revenuecat.api.base_url', 'https://api.revenuecat.com'),
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
        'secret' => config('app.revenuecat.webhook.secret'),
        'tolerance' => config('app.revenuecat.webhook.tolerance', 300),
        'endpoint' => config('app.revenuecat.webhook.endpoint', 'webhook/revenuecat'),
        'route_group' => config('app.revenuecat.webhook.route_group', 'web'),
        'route_middleware' => config('app.revenuecat.webhook.route_middleware', 'web'),
        'handler' => config('app.revenuecat.webhook.handler', \PeterSowah\LaravelCashierRevenueCat\Http\Controllers\RevenueCatWebhookController::class),
        'allowed_ips' => config('app.revenuecat.webhook.allowed_ips', ''),
        'rate_limit' => [
            'enabled' => config('app.revenuecat.webhook.rate_limit.enabled', true),
            'max_attempts' => config('app.revenuecat.webhook.rate_limit.max_attempts', 60),
            'decay_minutes' => config('app.revenuecat.webhook.rate_limit.decay_minutes', 1),
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
        'customers_table' => config('app.revenuecat.database.customers_table', 'customers'),
        'subscriptions_table' => config('app.revenuecat.database.subscriptions_table', 'subscriptions'),
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
        'enabled' => config('app.revenuecat.cache.enabled', true),
        'ttl' => config('app.revenuecat.cache.ttl', 3600),
        'prefix' => config('app.revenuecat.cache.prefix', 'revenuecat'),
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
        'enabled' => config('app.revenuecat.logging.enabled', true),
        'channel' => config('app.revenuecat.logging.channel', 'stack'),
        'level' => config('app.revenuecat.logging.level', 'debug'),
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
        'throw_exceptions' => config('app.revenuecat.error_handling.throw_exceptions', true),
        'log_errors' => config('app.revenuecat.error_handling.log_errors', true),
        'retry_on_error' => config('app.revenuecat.error_handling.retry_on_error', true),
        'max_retries' => config('app.revenuecat.error_handling.max_retries', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Other Configuration
    |--------------------------------------------------------------------------
    |
    | Additional configuration options for the package.
    |
    */

    'currency' => config('app.revenuecat.currency', 'USD'),
    'model' => [
        'user' => config('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class),
    ],
];
