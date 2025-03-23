<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use PeterSowah\LaravelCashierRevenueCat\Http\Controllers\WebhookController;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::post('revenuecat/webhook', [WebhookController::class, 'handleWebhook'])
        ->name('cashier.webhook');
});

test('it handles webhooks and dispatches events', function () {
    Event::fake();
    config(['cashier-revenue-cat.webhook.secret' => 'test-secret']);

    $payload = [
        'type' => 'initial_purchase',
        'event' => [
            'id' => 'test-event-id',
            'type' => 'INITIAL_PURCHASE',
        ],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test-secret');

    $response = $this->postJson('revenuecat/webhook', $payload, [
        'RevenueCat-Signature' => $signature,
    ]);

    $response->assertStatus(200);
    Event::assertDispatched(WebhookReceived::class, function ($event) use ($payload) {
        return $event->payload === $payload;
    });
});

test('it validates webhook signatures when configured', function () {
    config(['cashier-revenue-cat.webhook.secret' => 'test-secret']);

    $payload = [
        'type' => 'initial_purchase',
        'event' => [
            'id' => 'test-event-id',
            'type' => 'INITIAL_PURCHASE',
        ],
    ];

    $signature = hash_hmac('sha256', json_encode($payload), 'test-secret');

    $response = $this->postJson('revenuecat/webhook', $payload, [
        'RevenueCat-Signature' => $signature,
    ]);

    $response->assertStatus(200);
});

test('it rejects invalid webhook signatures', function () {
    config(['cashier-revenue-cat.webhook.secret' => 'test-secret']);

    $payload = [
        'type' => 'initial_purchase',
        'event' => [
            'id' => 'test-event-id',
            'type' => 'INITIAL_PURCHASE',
        ],
    ];

    $response = $this->postJson('revenuecat/webhook', $payload, [
        'RevenueCat-Signature' => 'invalid-signature',
    ]);

    $response->assertStatus(403);
});
