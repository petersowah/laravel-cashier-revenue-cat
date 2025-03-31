<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class WebhookSignature
{
    /**
     * Verify the webhook signature.
     */
    public static function verify(string $payload, string $signature): bool
    {
        $secret = Config::get('cashier-revenue-cat.webhook.secret');

        if (! $secret) {
            Log::error('Webhook secret not configured');

            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        Log::debug('Webhook signature comparison', [
            'received' => $signature,
            'expected' => $expectedSignature,
            'matches' => hash_equals($expectedSignature, $signature),
        ]);

        return hash_equals($expectedSignature, $signature);
    }
}
