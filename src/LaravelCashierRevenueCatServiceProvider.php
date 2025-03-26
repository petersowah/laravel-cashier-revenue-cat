<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use PeterSowah\LaravelCashierRevenueCat\Commands\LaravelCashierRevenueCatCommand;
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
                '2025_03_21_000001_create_customers_table',
                '2025_03_21_000002_create_subscriptions_table',
                '2025_03_21_000003_create_receipts_table',
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

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
