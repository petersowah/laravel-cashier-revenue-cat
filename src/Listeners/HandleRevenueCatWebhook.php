<?php

namespace PeterSowah\LaravelCashierRevenueCat\Listeners;

use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Contracts\WebhookPayload;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    /**
     * Handle the webhook event.
     */
    public function handle(WebhookReceived $event): void
    {
        /** @var array<string, mixed> $payload */
        $payload = $event->payload;
        $type = $payload['event']['type'];

        Log::info('RevenueCat webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        switch ($type) {
            case 'INITIAL_PURCHASE':
                $this->handleInitialPurchase($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
            case 'NON_RENEWING_PURCHASE':
                $this->handleNonRenewingPurchase($payload);
                break;
            case 'SUBSCRIPTION_PAUSED':
                $this->handleSubscriptionPaused($payload);
                break;
            case 'SUBSCRIPTION_RESUMED':
                $this->handleSubscriptionResumed($payload);
                break;
            case 'PRODUCT_CHANGE':
                $this->handleProductChange($payload);
                break;
            case 'BILLING_ISSUE':
                $this->handleBillingIssue($payload);
                break;
            case 'REFUND':
                $this->handleRefund($payload);
                break;
            case 'SUBSCRIPTION_PERIOD_CHANGED':
                $this->handleSubscriptionPeriodChanged($payload);
                break;
        }
    }

    /**
     * Handle initial purchase event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleInitialPurchase(array $payload): void
    {
        // Handle initial purchase
        Log::info('Handling initial purchase', ['payload' => $payload]);
    }

    /**
     * Handle renewal event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleRenewal(array $payload): void
    {
        // Handle renewal
        Log::info('Handling renewal', ['payload' => $payload]);
    }

    /**
     * Handle cancellation event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleCancellation(array $payload): void
    {
        // Handle cancellation
        Log::info('Handling cancellation', ['payload' => $payload]);
    }

    /**
     * Handle non-renewing purchase event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleNonRenewingPurchase(array $payload): void
    {
        // Handle non-renewing purchase
        Log::info('Handling non-renewing purchase', ['payload' => $payload]);
    }

    /**
     * Handle subscription paused event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleSubscriptionPaused(array $payload): void
    {
        // Handle subscription paused
        Log::info('Handling subscription paused', ['payload' => $payload]);
    }

    /**
     * Handle subscription resumed event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleSubscriptionResumed(array $payload): void
    {
        // Handle subscription resumed
        Log::info('Handling subscription resumed', ['payload' => $payload]);
    }

    /**
     * Handle product change event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleProductChange(array $payload): void
    {
        // Handle product change
        Log::info('Handling product change', ['payload' => $payload]);
    }

    /**
     * Handle billing issue event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleBillingIssue(array $payload): void
    {
        // Handle billing issue
        Log::info('Handling billing issue', ['payload' => $payload]);
    }

    /**
     * Handle refund event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleRefund(array $payload): void
    {
        // Handle refund
        Log::info('Handling refund', ['payload' => $payload]);
    }

    /**
     * Handle subscription period changed event.
     *
     * @param array<string, mixed> $payload
     */
    protected function handleSubscriptionPeriodChanged(array $payload): void
    {
        // Handle subscription period changed
        Log::info('Handling subscription period changed', ['payload' => $payload]);
    }
}
