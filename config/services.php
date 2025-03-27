<?php

return [
    'revenuecat' => [
        'key' => env('REVENUECAT_API_KEY'),
        'project_id' => env('REVENUECAT_PROJECT_ID'),
        'version' => env('REVENUECAT_API_VERSION', 'v2'),
        'base_url' => env('REVENUECAT_API_BASE_URL', 'https://api.revenuecat.com'),
        'webhook_secret' => env('REVENUECAT_WEBHOOK_SECRET'),
        'webhook_tolerance' => env('REVENUECAT_WEBHOOK_TOLERANCE', 300),
        'webhook_endpoint' => env('REVENUECAT_WEBHOOK_ENDPOINT', 'webhook/revenuecat'),
        'route_group' => env('REVENUECAT_ROUTE_GROUP', 'web'),
        'webhook_allowed_ips' => env('REVENUECAT_WEBHOOK_ALLOWED_IPS', ''),
        'webhook_rate_limit_enabled' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ENABLED', true),
        'webhook_rate_limit_attempts' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ATTEMPTS', 60),
        'webhook_rate_limit_decay' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_DECAY', 1),
        'cache_enabled' => env('REVENUECAT_CACHE_ENABLED', true),
        'cache_ttl' => env('REVENUECAT_CACHE_TTL', 3600),
        'cache_prefix' => env('REVENUECAT_CACHE_PREFIX', 'revenuecat'),
        'logging_enabled' => env('REVENUECAT_LOGGING_ENABLED', true),
        'logging_channel' => env('REVENUECAT_LOGGING_CHANNEL', 'stack'),
        'logging_level' => env('REVENUECAT_LOGGING_LEVEL', 'debug'),
        'throw_exceptions' => env('REVENUECAT_THROW_EXCEPTIONS', true),
        'log_errors' => env('REVENUECAT_LOG_ERRORS', true),
        'retry_on_error' => env('REVENUECAT_RETRY_ON_ERROR', true),
        'max_retries' => env('REVENUECAT_MAX_RETRIES', 3),
        'currency' => env('REVENUECAT_CURRENCY', 'USD'),
    ],
];
