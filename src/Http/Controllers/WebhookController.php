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
        $signature = $request->header('X-RevenueCat-Signature');

        if (! $signature) {
            Log::warning('RevenueCat webhook received without signature');
            throw new HttpException(400, 'Missing webhook signature');
        }

        if (! WebhookSignature::verify($request->getContent(), $signature)) {
            Log::warning('RevenueCat webhook signature verification failed');
            throw new HttpException(400, 'Invalid webhook signature');
        }

        event(new WebhookReceived($payload));

        return response('', 200);
    }
}
