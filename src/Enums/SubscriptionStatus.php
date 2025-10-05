<?php

namespace PeterSowah\LaravelCashierRevenueCat\Enums;

use InvalidArgumentException;

enum SubscriptionStatus: string
{
    /**
     * Initial purchase or active subscription
     */
    case ACTIVE = 'active';

    /**
     * Subscription has been cancelled but still within paid period
     */
    case CANCELED = 'canceled';

    /**
     * Subscription has expired and access should be revoked
     */
    case EXPIRED = 'expired';

    /**
     * Subscription is in grace period due to payment issues
     */
    case GRACE_PERIOD = 'grace_period';

    /**
     * Subscription is paused (Google Play only)
     */
    case PAUSED = 'paused';

    /**
     * Subscription is in trial period
     */
    case TRIAL = 'trial';

    /**
     * Get the description for the status
     */
    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active subscription',
            self::CANCELED => 'Cancelled but still active until period ends',
            self::EXPIRED => 'Subscription has expired',
            self::GRACE_PERIOD => 'In grace period due to payment issues',
            self::PAUSED => 'Subscription is paused',
            self::TRIAL => 'In trial period',
        };
    }

    /**
     * Check if the subscription is active
     */
    public function isActive(): bool
    {
        return match ($this) {
            self::ACTIVE, self::TRIAL, self::GRACE_PERIOD => true,
            default => false,
        };
    }

    /**
     * Get the status from a RevenueCat webhook event
     *
     * @throws InvalidArgumentException
     */
    public static function fromWebhookEvent(array $event): self
    {
        return match ($event['type']) {
            'INITIAL_PURCHASE' => isset($event['is_trial_period']) && $event['is_trial_period'] ? self::TRIAL : self::ACTIVE,
            'RENEWAL', 'UNCANCELLATION' => self::ACTIVE,
            'CANCELLATION' => self::CANCELED,
            'SUBSCRIPTION_PAUSED' => self::PAUSED,
            'BILLING_ISSUE' => self::GRACE_PERIOD,
            'EXPIRATION' => self::EXPIRED,
            default => throw new InvalidArgumentException("Unknown webhook event type: {$event['type']}"),
        };
    }
}
