<?php

return [
    'revenuecat' => [
        'key' => env('REVENUECAT_API_KEY'),
        'project_id' => env('REVENUECAT_PROJECT_ID'),
        'version' => env('REVENUECAT_API_VERSION', 'v2'),
        'base_url' => env('REVENUECAT_API_BASE_URL', 'https://api.revenuecat.com'),
    ],
];
