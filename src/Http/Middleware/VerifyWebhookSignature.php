<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-RevenueCat-Signature');

        if (! $signature) {
            abort(403, 'No signature provided');
        }

        $payload = $request->getContent();
        $secret = config('cashier-revenue-cat.webhook.secret');

        if (! $secret) {
            abort(403, 'Webhook secret not configured');
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid signature');
        }

        return $next($request);
    }
}
