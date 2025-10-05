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
                    function () {
                        $handler = config('cashier-revenue-cat.webhook.handler');
                        if (class_exists(\App\Http\Controllers\WebhookController::class)) {
                            return app(\App\Http\Controllers\WebhookController::class)->handleWebhook(request());
                        }

                        return app(\PeterSowah\LaravelCashierRevenueCat\Http\Controllers\RevenueCatWebhookController::class)->handleWebhook(request());
                    }
                )->name('cashier-revenue-cat.webhook');
            });
    }
}
