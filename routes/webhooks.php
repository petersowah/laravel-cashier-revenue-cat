<?php

use Illuminate\Support\Facades\Route;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;

Route::post('revenuecat/webhook', [WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook'); 