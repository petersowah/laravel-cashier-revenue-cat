<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;
use PeterSowah\LaravelCashierRevenueCat\Http\Middleware\VerifyRevenueCatWebhook;

class RevenueCatServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // First merge the services config
        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php',
            'services'
        );

        // Then merge our package config
        $this->mergeConfigFrom(
            __DIR__.'/../config/cashier-revenue-cat.php',
            'cashier-revenue-cat'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cashier-revenue-cat.php' => config_path('cashier-revenue-cat.php'),
            ], 'revenuecat-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'revenuecat-migrations');
        }

        $this->registerWebhookRoute();
        $this->registerMiddleware();
    }

    /**
     * Register the webhook route.
     */
    protected function registerWebhookRoute(): void
    {
        $endpoint = config('services.revenuecat.webhook_endpoint', 'webhook/revenuecat');

        Route::post($endpoint, [WebhookController::class, 'handleWebhook'])
            ->name('cashier-revenue-cat.webhook')
            ->middleware(['revenuecat'])
            ->withoutMiddleware(['csrf']);
    }

    /**
     * Register the middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('revenuecat', VerifyRevenueCatWebhook::class);
    }
}
