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
            ], 'config');
        }

        $this->registerWebhookRoute();
        $this->registerMiddleware();
    }

    /**
     * Register the webhook route.
     */
    protected function registerWebhookRoute(): void
    {
        Route::post(config('cashier-revenue-cat.webhook.endpoint'), [WebhookController::class, 'handleWebhook'])
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
