<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider;
use PeterSowah\LaravelCashierRevenueCat\Tests\Fixtures\User;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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
        config()->set('cashier-revenue-cat.model.user', User::class);
    }

    protected function createCustomer($description = 'peter'): User
    {
        return User::create([
            'email' => "{$description}@example.com",
            'name' => 'Peter Sowah',
        ]);
    }
}
