<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelCashierRevenueCatServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('cashier-revenue-cat.api_key', 'test-api-key');
        config()->set('cashier-revenue-cat.webhook.secret', 'test-webhook-secret');
    }
}
