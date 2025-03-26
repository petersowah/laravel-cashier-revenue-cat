<?php

return [
    'revenuecat' => [
        'key' => config('cashier-revenue-cat.api.key'),
        'project_id' => config('cashier-revenue-cat.api.project_id'),
        'version' => config('cashier-revenue-cat.api.version', 'v2'),
        'base_url' => config('cashier-revenue-cat.api.base_url', 'https://api.revenuecat.com'),
        'webhook_secret' => config('cashier-revenue-cat.webhook.secret'),
        'webhook_tolerance' => config('cashier-revenue-cat.webhook.tolerance', 300),
        'webhook_endpoint' => config('cashier-revenue-cat.webhook.endpoint', 'webhook/revenuecat'),
        'cache_enabled' => config('cashier-revenue-cat.cache.enabled', true),
        'cache_ttl' => config('cashier-revenue-cat.cache.ttl', 3600),
        'cache_prefix' => config('cashier-revenue-cat.cache.prefix', 'revenuecat'),
        'logging_enabled' => config('cashier-revenue-cat.logging.enabled', true),
        'logging_channel' => config('cashier-revenue-cat.logging.channel', 'stack'),
        'logging_level' => config('cashier-revenue-cat.logging.level', 'debug'),
        'throw_exceptions' => config('cashier-revenue-cat.error_handling.throw_exceptions', true),
        'log_errors' => config('cashier-revenue-cat.error_handling.log_errors', true),
        'retry_on_error' => config('cashier-revenue-cat.error_handling.retry_on_error', true),
        'max_retries' => config('cashier-revenue-cat.error_handling.max_retries', 3),
        'currency' => config('cashier-revenue-cat.currency', 'USD'),
    ],
];
