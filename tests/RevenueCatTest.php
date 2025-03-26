<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PeterSowah\LaravelCashierRevenueCat\RevenueCat;
use PHPUnit\Framework\Attributes\Test;

class RevenueCatTest extends TestCase
{
    protected RevenueCat $revenueCat;

    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler;
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->revenueCat = new RevenueCat(config('cashier-revenue-cat.api.key'));
        $this->revenueCat->setClient($client);
    }

    #[Test]
    public function it_can_get_a_subscriber()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'management_url' => 'https://revenuecat.com/manage',
                'entitlements' => [
                    'premium' => [
                        'identifier' => 'premium',
                        'is_active' => true,
                        'will_renew' => true,
                        'period_type' => 'NORMAL',
                        'latest_purchase_date' => '2024-03-23T00:00:00Z',
                        'original_purchase_date' => '2024-03-23T00:00:00Z',
                        'expiration_date' => '2024-04-23T00:00:00Z',
                    ],
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriber('test-user');

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertEquals('test-user', $response['subscriber']['original_app_user_id']);
        $this->assertEquals('https://revenuecat.com/manage', $response['subscriber']['management_url']);
        $this->assertArrayHasKey('entitlements', $response['subscriber']);
    }

    #[Test]
    public function it_can_create_a_subscriber()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'management_url' => 'https://revenuecat.com/manage',
                'entitlements' => [],
            ],
        ])));

        $response = $this->revenueCat->createSubscriber('test-user', [
            'attributes' => ['name' => 'Test User'],
        ]);

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertEquals('test-user', $response['subscriber']['original_app_user_id']);
    }

    #[Test]
    public function it_can_get_offerings()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'current_offering_id' => 'test-offering',
            'offerings' => [
                [
                    'identifier' => 'test-offering',
                    'description' => 'Test Offering',
                    'packages' => [
                        [
                            'identifier' => 'test-package',
                            'platform_product_identifier' => 'com.example.test',
                            'store_product' => [
                                'identifier' => 'com.example.test',
                                'price' => 9.99,
                                'price_string' => '$9.99',
                                'currency_code' => 'USD',
                            ],
                        ],
                    ],
                ],
            ],
        ])));

        $response = $this->revenueCat->getOfferings();

        $this->assertArrayHasKey('current_offering_id', $response);
        $this->assertArrayHasKey('offerings', $response);
        $this->assertEquals('test-offering', $response['current_offering_id']);
    }

    #[Test]
    public function it_can_get_products()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'products' => [
                [
                    'identifier' => 'test-product',
                    'description' => 'Test Product',
                    'price' => 9.99,
                    'currency' => 'USD',
                ],
            ],
        ])));

        $response = $this->revenueCat->getProducts();

        $this->assertArrayHasKey('products', $response);
        $this->assertEquals('test-product', $response['products'][0]['identifier']);
    }

    #[Test]
    public function it_can_get_subscriber_history()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'transactions' => [
                [
                    'transaction_id' => 'test-transaction',
                    'product_id' => 'test-product',
                    'purchase_date' => '2024-03-23T00:00:00Z',
                    'expiration_date' => '2024-04-23T00:00:00Z',
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberHistory('test-user');

        $this->assertArrayHasKey('transactions', $response);
        $this->assertEquals('test-transaction', $response['transactions'][0]['transaction_id']);
    }

    #[Test]
    public function it_can_get_subscriber_entitlements()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'entitlements' => [
                'premium' => [
                    'identifier' => 'premium',
                    'is_active' => true,
                    'will_renew' => true,
                    'period_type' => 'NORMAL',
                    'latest_purchase_date' => '2024-03-23T00:00:00Z',
                    'original_purchase_date' => '2024-03-23T00:00:00Z',
                    'expiration_date' => '2024-04-23T00:00:00Z',
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberEntitlements('test-user');

        $this->assertArrayHasKey('entitlements', $response);
        $this->assertArrayHasKey('premium', $response['entitlements']);
        $this->assertTrue($response['entitlements']['premium']['is_active']);
    }

    #[Test]
    public function it_can_get_subscriber_purchases()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'purchases' => [
                [
                    'transaction_id' => 'test-transaction',
                    'product_id' => 'test-product',
                    'purchase_date' => '2024-03-23T00:00:00Z',
                    'expiration_date' => '2024-04-23T00:00:00Z',
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberPurchases('test-user');

        $this->assertArrayHasKey('purchases', $response);
        $this->assertEquals('test-transaction', $response['purchases'][0]['transaction_id']);
    }

    #[Test]
    public function it_can_get_user_subscriptions()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'entitlements' => [
                    'premium' => [
                        'identifier' => 'premium',
                        'is_active' => true,
                        'will_renew' => true,
                        'period_type' => 'NORMAL',
                        'latest_purchase_date' => '2024-03-23T00:00:00Z',
                        'original_purchase_date' => '2024-03-23T00:00:00Z',
                        'expiration_date' => '2024-04-23T00:00:00Z',
                    ],
                ],
            ],
        ])));

        $response = $this->revenueCat->getUserSubscriptions('test-user');

        $this->assertArrayHasKey('premium', $response);
        $this->assertTrue($response['premium']['is_active']);
        $this->assertEquals('premium', $response['premium']['identifier']);
    }

    #[Test]
    public function it_can_get_subscriber_offering()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'current_offering_id' => 'test-offering',
            'offerings' => [
                [
                    'identifier' => 'test-offering',
                    'description' => 'Test Offering',
                    'packages' => [
                        [
                            'identifier' => 'test-package',
                            'platform_product_identifier' => 'com.example.test',
                            'store_product' => [
                                'identifier' => 'com.example.test',
                                'price' => 9.99,
                                'price_string' => '$9.99',
                                'currency_code' => 'USD',
                            ],
                        ],
                    ],
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberOffering('test-user');

        $this->assertArrayHasKey('current_offering_id', $response);
        $this->assertArrayHasKey('offerings', $response);
        $this->assertEquals('test-offering', $response['current_offering_id']);
    }

    #[Test]
    public function it_can_get_subscriber_non_subscriptions()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'non_subscriptions' => [
                [
                    'product_id' => 'test-product',
                    'purchase_date' => '2024-03-23T00:00:00Z',
                    'expiration_date' => '2024-04-23T00:00:00Z',
                    'store' => 'app_store',
                    'transaction_id' => 'test-transaction',
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberNonSubscriptions('test-user');

        $this->assertArrayHasKey('non_subscriptions', $response);
        $this->assertEquals('test-product', $response['non_subscriptions'][0]['product_id']);
    }

    #[Test]
    public function it_can_get_subscriber_subscriptions()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'entitlements' => [
                    'premium' => [
                        'identifier' => 'premium',
                        'is_active' => true,
                        'will_renew' => true,
                        'period_type' => 'NORMAL',
                        'latest_purchase_date' => '2024-03-23T00:00:00Z',
                        'original_purchase_date' => '2024-03-23T00:00:00Z',
                        'expiration_date' => '2024-04-23T00:00:00Z',
                    ],
                ],
            ],
        ])));

        $response = $this->revenueCat->getSubscriberSubscriptions('test-user');

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertArrayHasKey('entitlements', $response['subscriber']);
        $this->assertArrayHasKey('premium', $response['subscriber']['entitlements']);
    }
}
