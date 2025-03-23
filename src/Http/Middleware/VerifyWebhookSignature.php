<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PeterSowah\LaravelCashierRevenueCat\Exceptions\WebhookSignatureException;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('cashier-revenue-cat.webhook.secret')) {
            return $next($request);
        }

        try {
            $signature = $request->header('RevenueCat-Signature');
            $payload = $request->getContent();
            $secret = config('cashier-revenue-cat.webhook.secret');

            if (! $this->isValidSignature($signature, $payload, $secret)) {
                throw new WebhookSignatureException('Invalid webhook signature.');
            }

            return $next($request);
        } catch (WebhookSignatureException $e) {
            abort(403, $e->getMessage());
        }
    }

    protected function isValidSignature(?string $signature, string $payload, string $secret): bool
    {
        if (! $signature) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computedSignature, $signature);
    }
} 