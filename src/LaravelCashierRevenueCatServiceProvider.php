<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use PeterSowah\LaravelCashierRevenueCat\Providers\RouteServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCashierRevenueCatServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cashier-revenue-cat')
            ->hasConfigFile('cashier-revenue-cat')
            ->hasViews()
            ->hasMigrations([
                'create_customers_table',
                'create_subscriptions_table',
            ])
            ->hasCommand(\PeterSowah\LaravelCashierRevenueCat\Commands\PublishWebhookHandlerCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RevenueCat::class, function ($app) {
            return new RevenueCat(config('cashier-revenue-cat.api.key'), config('cashier-revenue-cat.api.project_id'));
        });

        $this->app->alias(RevenueCat::class, 'revenuecat');
    }

    public function register(): void
    {
        parent::register();
        $this->app->register(RouteServiceProvider::class);
    }
}
