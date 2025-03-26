<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use PeterSowah\LaravelCashierRevenueCat\Http\Middleware\VerifyWebhookSignature;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    public function __construct()
    {
        if (config('cashier-revenue-cat.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    public function handleWebhook(Request $request): Response
    {
        $payload = $request->all();
        $eventType = $payload['event']['type'] ?? $payload['type'] ?? null;

        if (! $eventType) {
            return new Response('Invalid webhook payload', 400);
        }

        $method = 'handle'.Str::studly($eventType);

        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            $response = $this->{$method}($payload);

            return $response;
        }

        return new Response('Webhook Handled', 200);
    }

    protected function handleInitialPurchase(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle initial purchase webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleRenewal(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle renewal webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleCancellation(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle cancellation webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleSubscriptionPaused(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle subscription paused webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleSubscriptionResumed(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle subscription resumed webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleRefund(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle refund webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleNonRenewingPurchase(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle non-renewing purchase webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleSubscriptionPeriodChanged(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle subscription period changed webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleBillingIssue(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle billing issue webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleProductChange(array $payload): Response
    {
        $event = $payload['event'] ?? [];
        $subscriber = $event['subscriber'] ?? [];
        $entitlements = $subscriber['entitlements'] ?? [];

        // Handle product change webhook
        return new Response('Webhook Handled', 200);
    }
}
