<?php

// config for PeterSowah/LaravelCashierRevenueCat
return [
    'api_key' => config('services.revenuecat.api_key'),

    'webhook' => [
        'secret' => config('services.revenuecat.webhook.secret'),
        'tolerance' => config('services.revenuecat.webhook.tolerance', 300),
    ],

    'currency' => config('services.revenuecat.currency', 'USD'),

    'model' => [
        'user' => config('auth.providers.users.model', \App\Models\User::class),
    ],
];
