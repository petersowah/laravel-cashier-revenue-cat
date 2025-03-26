<?php

return [
    'revenuecat' => [
        'key' => env('REVENUE_CAT_API_KEY'),
        'project_id' => env('REVENUE_CAT_PROJECT_ID'),
        'version' => env('REVENUE_CAT_API_VERSION', 'v2'),
        'base_url' => env('REVENUE_CAT_API_BASE_URL', 'https://api.revenuecat.com'),
        'webhook_secret' => env('REVENUE_CAT_WEBHOOK_SECRET'),
        'webhook_tolerance' => env('REVENUE_CAT_WEBHOOK_TOLERANCE', 300),
        'webhook_endpoint' => env('REVENUE_CAT_WEBHOOK_ENDPOINT', 'webhook/revenuecat'),
        'cache_enabled' => env('REVENUE_CAT_CACHE_ENABLED', true),
        'cache_ttl' => env('REVENUE_CAT_CACHE_TTL', 3600),
        'cache_prefix' => env('REVENUE_CAT_CACHE_PREFIX', 'revenuecat'),
        'logging_enabled' => env('REVENUE_CAT_LOGGING_ENABLED', true),
        'logging_channel' => env('REVENUE_CAT_LOGGING_CHANNEL', 'stack'),
        'logging_level' => env('REVENUE_CAT_LOGGING_LEVEL', 'debug'),
        'throw_exceptions' => env('REVENUE_CAT_THROW_EXCEPTIONS', true),
        'log_errors' => env('REVENUE_CAT_LOG_ERRORS', true),
        'retry_on_error' => env('REVENUE_CAT_RETRY_ON_ERROR', true),
        'max_retries' => env('REVENUE_CAT_MAX_RETRIES', 3),
        'currency' => env('REVENUE_CAT_CURRENCY', 'USD'),
    ],
];
