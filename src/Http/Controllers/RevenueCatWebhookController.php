<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RevenueCatWebhookController
{
    /**
     * Handle incoming RevenueCat webhook requests.
     */
    public function handleWebhook(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->all();

        // Log the incoming webhook request
        Log::info('RevenueCat webhook received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event_type' => $payload['event']['type'] ?? 'unknown',
            'event_id' => $payload['event']['id'] ?? null,
        ]);

        $authHeader = $request->header('Authorization');

        if (! $authHeader) {
            Log::warning('RevenueCat webhook authorization header missing', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            throw new HttpException(401, 'Missing authorization header');
        }

        // Verify the authorization header
        $expectedAuth = 'Bearer '.config('cashier-revenue-cat.webhook.secret');
        if ($authHeader !== $expectedAuth) {
            Log::warning('RevenueCat webhook authorization header invalid', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'received' => $authHeader,
                'expected' => $expectedAuth,
            ]);
            throw new HttpException(401, 'Invalid authorization header');
        }

        // Log successful webhook processing
        Log::info('RevenueCat webhook processed successfully', [
            'event_type' => $payload['event']['type'] ?? 'unknown',
            'event_id' => $payload['event']['id'] ?? null,
            'subscriber_id' => $payload['event']['subscriber']['id'] ?? null,
            'product_id' => $payload['event']['product']['id'] ?? null,
        ]);

        event(new WebhookReceived($payload));

        return response('', 200);
    }
}
