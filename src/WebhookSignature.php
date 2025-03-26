<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Support\Facades\Config;

class WebhookSignature
{
    /**
     * Verify the webhook signature.
     */
    public static function verify(string $payload, string $signature): bool
    {
        $secret = Config::get('cashier-revenue-cat.webhook.secret');

        if (! $secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
} 