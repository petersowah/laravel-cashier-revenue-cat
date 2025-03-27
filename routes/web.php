<?php

use Illuminate\Support\Facades\Route;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;

$endpoint = config('services.revenuecat.webhook_endpoint', 'webhook/revenuecat');
$routeGroup = config('services.revenuecat.route_group', 'web');

// Route::group(['middleware' => $routeGroup], function () use ($endpoint) {
//     Route::post($endpoint, [WebhookController::class, 'handleWebhook'])
//         ->name('cashier-revenue-cat.webhook')
//         ->middleware(['revenuecat'])
//         ->withoutMiddleware(['csrf']);
// });
