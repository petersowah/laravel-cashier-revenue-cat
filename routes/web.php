<?php

use Illuminate\Support\Facades\Route;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;

$endpoint = config('cashier-revenue-cat.webhook.endpoint', 'webhook/revenuecat');

Route::post($endpoint, [WebhookController::class, 'handleWebhook'])
    ->name('cashier-revenue-cat.webhook');
