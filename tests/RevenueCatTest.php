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

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->revenueCat = new RevenueCat('test-api-key');
        $this->revenueCat->setClient($client);
    }

    #[Test]
    public function it_can_get_a_subscriber()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'management_url' => 'https://revenuecat.com/manage',
            ],
        ])));

        $response = $this->revenueCat->getSubscriber('test-user');

        $this->assertArrayHasKey('subscriber', $response);
        $this->assertEquals('test-user', $response['subscriber']['original_app_user_id']);
        $this->assertEquals('https://revenuecat.com/manage', $response['subscriber']['management_url']);
    }

    #[Test]
    public function it_can_create_a_subscriber()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'subscriber' => [
                'original_app_user_id' => 'test-user',
                'management_url' => 'https://revenuecat.com/manage',
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
                    'type' => 'subscription',
                ],
            ],
        ])));

        $response = $this->revenueCat->getProducts();

        $this->assertArrayHasKey('products', $response);
        $this->assertEquals('test-product', $response['products'][0]['identifier']);
    }
} 