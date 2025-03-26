<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use PeterSowah\LaravelCashierRevenueCat\Commands\LaravelCashierRevenueCatCommand;
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
                'create_revenue_cat_customers_table',
                'create_revenue_cat_subscriptions_table',
                'create_revenue_cat_receipts_table',
            ])
            ->hasRoute('webhooks')
            ->hasCommand(LaravelCashierRevenueCatCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RevenueCat::class, function ($app) {
            return new RevenueCat(config('cashier-revenue-cat.api_key'));
        });
    }
}
