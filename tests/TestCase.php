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
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelCashierRevenueCatServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        // Use SQLite in memory for testing
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Package configuration
        config()->set('cashier-revenue-cat.api.key', 'test-api-key');
        config()->set('cashier-revenue-cat.api.project_id', 'test-project-id');
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
