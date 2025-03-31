<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PeterSowah\LaravelCashierRevenueCat\Facades\RevenueCat;
use PHPUnit\Framework\Attributes\Test;

class RevenueCatTest extends TestCase
{
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler;
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        RevenueCat::setClient($client);
    }

    #[Test]
    public function it_can_get_a_customer()
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

        $response = RevenueCat::getCustomer('test-user');

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertEquals('test-user', $response['subscriber']['original_app_user_id']);
        $this->assertEquals('https://revenuecat.com/manage', $response['subscriber']['management_url']);
        $this->assertArrayHasKey('entitlements', $response['subscriber']);
    }

    #[Test]
    public function it_can_create_a_customer()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'management_url' => 'https://revenuecat.com/manage',
                'entitlements' => [],
            ],
        ])));

        $response = RevenueCat::createCustomer('test-user', [
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

        $response = RevenueCat::getOfferings();

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

        $response = RevenueCat::getProducts();

        $this->assertArrayHasKey('products', $response);
        $this->assertEquals('test-product', $response['products'][0]['identifier']);
    }

    #[Test]
    public function it_can_get_customer_history()
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

        $response = RevenueCat::getCustomerHistory('test-user');

        $this->assertArrayHasKey('transactions', $response);
        $this->assertEquals('test-transaction', $response['transactions'][0]['transaction_id']);
    }

    #[Test]
    public function it_can_get_customer_entitlements()
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

        $response = RevenueCat::getCustomerEntitlements('test-user');

        $this->assertArrayHasKey('entitlements', $response);
        $this->assertArrayHasKey('premium', $response['entitlements']);
        $this->assertTrue($response['entitlements']['premium']['is_active']);
    }

    #[Test]
    public function it_can_get_customer_purchases()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'purchases' => [
                [
                    'product_id' => 'test-product',
                    'purchase_date' => '2024-03-23T00:00:00Z',
                    'expiration_date' => '2024-04-23T00:00:00Z',
                    'store' => 'app_store',
                    'transaction_id' => 'test-transaction',
                ],
            ],
        ])));

        $response = RevenueCat::getCustomerPurchases('test-user');

        $this->assertArrayHasKey('purchases', $response);
        $this->assertEquals('test-product', $response['purchases'][0]['product_id']);
    }

    #[Test]
    public function it_can_get_user_subscriptions()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
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

        $response = RevenueCat::getUserSubscriptions('test-user');

        $this->assertArrayHasKey('premium', $response);
        $this->assertTrue($response['premium']['is_active']);
    }

    #[Test]
    public function it_can_get_customer_offering()
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

        $response = RevenueCat::getCustomerOffering('test-user');

        $this->assertArrayHasKey('current_offering_id', $response);
        $this->assertArrayHasKey('offerings', $response);
        $this->assertEquals('test-offering', $response['current_offering_id']);
    }

    #[Test]
    public function it_can_get_customer_non_subscriptions()
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

        $response = RevenueCat::getCustomerNonSubscriptions('test-user');

        $this->assertArrayHasKey('non_subscriptions', $response);
        $this->assertEquals('test-product', $response['non_subscriptions'][0]['product_id']);
    }

    #[Test]
    public function it_can_get_customer_subscriptions()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'subscriptions' => [
                    'test-subscription' => [
                        'product_id' => 'test-product',
                        'purchase_date' => '2024-03-23T00:00:00Z',
                        'expiration_date' => '2024-04-23T00:00:00Z',
                        'store' => 'app_store',
                        'transaction_id' => 'test-transaction',
                    ],
                ],
            ],
        ])));

        $response = RevenueCat::getCustomerSubscriptions('test-user');

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertArrayHasKey('subscriptions', $response['subscriber']);
        $this->assertEquals('test-product', $response['subscriber']['subscriptions']['test-subscription']['product_id']);
    }

    #[Test]
    public function it_can_get_subscription_name()
    {
        $entitlement = [
            'identifier' => 'premium',
            'is_active' => true,
            'will_renew' => true,
            'period_type' => 'NORMAL',
            'latest_purchase_date' => '2024-03-23T00:00:00Z',
            'original_purchase_date' => '2024-03-23T00:00:00Z',
            'expiration_date' => '2024-04-23T00:00:00Z',
        ];

        $name = RevenueCat::getSubscriptionName($entitlement);

        $this->assertEquals('premium', $name);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_entitlement()
    {
        $entitlement = [
            'is_active' => true,
            'will_renew' => true,
            'period_type' => 'NORMAL',
        ];

        $name = RevenueCat::getSubscriptionName($entitlement);

        $this->assertEquals('', $name);
    }

    #[Test]
    public function it_can_get_subscription_name_from_webhook_event()
    {
        $webhookEvent = [
            'api_version' => '1.0',
            'event' => [
                'entitlement_ids' => ['pro'],
                'product_id' => 'pro_monthly',
                'type' => 'INITIAL_PURCHASE',
                'transaction_id' => '2000000885927737',
            ],
        ];

        $name = RevenueCat::getSubscriptionName($webhookEvent['event']);

        $this->assertEquals('pro', $name);
    }

    #[Test]
    public function it_returns_empty_string_for_webhook_event_without_entitlement_ids()
    {
        $webhookEvent = [
            'api_version' => '1.0',
            'event' => [
                'product_id' => 'pro_monthly',
                'type' => 'INITIAL_PURCHASE',
                'transaction_id' => '2000000885927737',
            ],
        ];

        $name = RevenueCat::getSubscriptionName($webhookEvent['event']);

        $this->assertEquals('', $name);
    }
}
