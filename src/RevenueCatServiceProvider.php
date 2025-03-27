<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;

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
    }

    /**
     * Register the webhook route.
     */
    protected function registerWebhookRoute(): void
    {
        Route::post(config('cashier-revenue-cat.webhook.endpoint'), [WebhookController::class, 'handleWebhook'])
            ->name('cashier-revenue-cat.webhook')
            ->middleware('web')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    }
} 