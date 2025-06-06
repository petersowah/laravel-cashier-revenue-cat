<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests;

use Illuminate\Support\Facades\Event;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use PHPUnit\Framework\Attributes\Test;

class WebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    protected function defineEnvironment($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('cashier-revenue-cat.webhook.secret', 'test-secret');
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    #[Test]
    public function it_validates_webhook_signature()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->postJson(
            route('cashier-revenue-cat.webhook'),
            $this->getWebhookPayload(),
            ['Authorization' => 'Bearer invalid_secret']
        );
    }

    #[Test]
    public function it_rejects_webhook_without_signature()
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->postJson(
            route('cashier-revenue-cat.webhook'),
            $this->getWebhookPayload()
        );
    }

    #[Test]
    public function it_handles_webhooks()
    {
        $payload = $this->getWebhookPayload();

        $this->postJson(
            route('cashier-revenue-cat.webhook'),
            $payload,
            ['Authorization' => 'Bearer '.config('cashier-revenue-cat.webhook.secret')]
        );

        Event::assertDispatched(WebhookReceived::class, function ($event) {
            return $event->payload['event']['type'] === 'INITIAL_PURCHASE';
        });
    }

    protected function getWebhookPayload(): array
    {
        return [
            'event' => [
                'type' => 'INITIAL_PURCHASE',
                'id' => 'evt_123',
                'created_at' => 1234567890,
                'subscriber' => [
                    'id' => 'sub_123',
                    'entitlements' => [
                        'premium' => [
                            'identifier' => 'premium',
                            'is_active' => true,
                            'will_renew' => true,
                            'period_type' => 'NORMAL',
                            'latest_purchase_date' => '2024-01-01T00:00:00Z',
                            'original_purchase_date' => '2024-01-01T00:00:00Z',
                            'expiration_date' => '2024-02-01T00:00:00Z',
                            'is_sandbox' => false,
                        ],
                    ],
                ],
                'product' => [
                    'id' => 'prod_123',
                    'identifier' => 'premium_monthly',
                    'price' => 9.99,
                    'currency' => 'USD',
                ],
            ],
        ];
    }

    protected function generateSignature(string $content): string
    {
        $secret = config('cashier-revenue-cat.webhook.secret');

        return hash_hmac('sha256', $content, $secret);
    }
}
