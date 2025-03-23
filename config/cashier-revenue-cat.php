<?php

// config for PeterSowah/LaravelCashierRevenueCat
return [
    'api_key' => env('REVENUECAT_API_KEY'),

    'webhook' => [
        'secret' => env('REVENUECAT_WEBHOOK_SECRET'),
        'tolerance' => env('REVENUECAT_WEBHOOK_TOLERANCE', 300),
    ],

    'currency' => env('CASHIER_CURRENCY', 'USD'),

    'model' => [
        'user' => env('CASHIER_MODEL', \App\Models\User::class),
    ],
];
