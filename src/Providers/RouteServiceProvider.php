<?php

namespace PeterSowah\LaravelCashierRevenueCat\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureRoutes();
    }

    /**
     * Configure the routes offered by the application.
     */
    protected function configureRoutes(): void
    {
        Route::middleware(config('cashier-revenue-cat.webhook.route_group', 'web'))
            ->withoutMiddleware(config('cashier-revenue-cat.webhook.route_middleware', 'web'))
            ->group(function () {
                Route::post(
                    config('cashier-revenue-cat.webhook.endpoint', 'webhook/revenuecat'),
                    config('cashier-revenue-cat.webhook.handler', \PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController::class . '@handleWebhook')
                )->name('cashier-revenue-cat.webhook');
            });
    }
}
