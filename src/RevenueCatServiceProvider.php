<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use PeterSowah\LaravelCashierRevenueCat\Http\Middleware\VerifyRevenueCatWebhook;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RevenueCatServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cashier-revenue-cat')
            ->hasConfigFile('revenuecat')
            ->hasMigrations([
                'create_revenuecat_subscriptions_table',
                'create_revenuecat_entitlements_table',
            ])
            ->hasRoute('web')
            ->hasMiddleware('revenuecat', VerifyRevenueCatWebhook::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        // First merge the services config
        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php',
            'services'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
