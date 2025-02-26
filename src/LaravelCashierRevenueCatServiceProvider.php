<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use PeterSowah\LaravelCashierRevenueCat\Commands\LaravelCashierRevenueCatCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_cashier_revenue_cat_table')
            ->hasCommand(LaravelCashierRevenueCatCommand::class);
    }
}
