<?php

namespace PeterSowah\LaravelCashierRevenueCat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class VerifyRevenueCatWebhook
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rate limiting
        $key = 'revenuecat-webhook:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response('Too Many Requests', 429);
        }
        RateLimiter::hit($key, 60);

        // IP whitelisting
        $allowedIps = config('cashier-revenue-cat.webhook.allowed_ips', []);
        if (! empty($allowedIps) && ! in_array($request->ip(), $allowedIps)) {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
