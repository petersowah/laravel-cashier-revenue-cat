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
        $method = 'handle'.Str::studly($payload['type']);

        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            $response = $this->{$method}($payload);

            return $response;
        }

        return new Response('Webhook Handled', 200);
    }

    protected function handleInitialPurchase(array $payload): Response
    {
        // Handle initial purchase webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleRenewal(array $payload): Response
    {
        // Handle renewal webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleCancellation(array $payload): Response
    {
        // Handle cancellation webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleSubscriptionPaused(array $payload): Response
    {
        // Handle subscription paused webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleSubscriptionResumed(array $payload): Response
    {
        // Handle subscription resumed webhook
        return new Response('Webhook Handled', 200);
    }

    protected function handleRefund(array $payload): Response
    {
        // Handle refund webhook
        return new Response('Webhook Handled', 200);
    }
} 