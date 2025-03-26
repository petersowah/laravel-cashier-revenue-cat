<?php

return [
    'revenuecat' => [
        'key' => config('revenuecat.secret_key'),
        'project_id' => config('revenuecat.project_id'),
        'version' => config('revenuecat.api_version', 'v2'),
        'base_url' => config('revenuecat.api_base_url', 'https://api.revenuecat.com'),
        'webhook_secret' => config('revenuecat.webhook_secret'),
        'webhook_tolerance' => config('revenuecat.webhook_tolerance', 300),
        'webhook_endpoint' => config('revenuecat.webhook_endpoint', 'webhook/revenuecat'),
        'cache_enabled' => config('revenuecat.cache_enabled', true),
        'cache_ttl' => config('revenuecat.cache_ttl', 3600),
        'cache_prefix' => config('revenuecat.cache_prefix', 'revenuecat'),
        'logging_enabled' => config('revenuecat.logging_enabled', true),
        'logging_channel' => config('revenuecat.logging_channel', 'stack'),
        'logging_level' => config('revenuecat.logging_level', 'debug'),
        'throw_exceptions' => config('revenuecat.throw_exceptions', true),
        'log_errors' => config('revenuecat.log_errors', true),
        'retry_on_error' => config('revenuecat.retry_on_error', true),
        'max_retries' => config('revenuecat.max_retries', 3),
        'currency' => config('revenuecat.currency', 'USD'),
    ],
];
