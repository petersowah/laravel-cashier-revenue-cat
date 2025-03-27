<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use PeterSowah\LaravelCashierRevenueCat\WebhookSignature;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WebhookController
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

        $signature = $request->header('X-RevenueCat-Signature');

        if (! $signature) {
            Log::warning('RevenueCat webhook signature missing', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            throw new HttpException(400, 'Missing webhook signature');
        }

        if (! WebhookSignature::verify($request->getContent(), $signature)) {
            Log::warning('RevenueCat webhook signature verification failed', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'event_type' => $payload['event']['type'] ?? 'unknown',
                'event_id' => $payload['event']['id'] ?? null,
            ]);
            throw new HttpException(400, 'Invalid webhook signature');
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
