<?php

use PeterSowah\LaravelCashierRevenueCat\Enums\SubscriptionStatus;

test('it has correct string values', function () {
    expect(SubscriptionStatus::ACTIVE->value)->toBe('active')
        ->and(SubscriptionStatus::CANCELED->value)->toBe('canceled')
        ->and(SubscriptionStatus::EXPIRED->value)->toBe('expired')
        ->and(SubscriptionStatus::GRACE_PERIOD->value)->toBe('grace_period')
        ->and(SubscriptionStatus::PAUSED->value)->toBe('paused')
        ->and(SubscriptionStatus::TRIAL->value)->toBe('trial');
});

test('it has correct descriptions', function () {
    expect(SubscriptionStatus::ACTIVE->description())->toBe('Active subscription')
        ->and(SubscriptionStatus::CANCELED->description())->toBe('Cancelled but still active until period ends')
        ->and(SubscriptionStatus::EXPIRED->description())->toBe('Subscription has expired')
        ->and(SubscriptionStatus::GRACE_PERIOD->description())->toBe('In grace period due to payment issues')
        ->and(SubscriptionStatus::PAUSED->description())->toBe('Subscription is paused')
        ->and(SubscriptionStatus::TRIAL->description())->toBe('In trial period');
});

test('it correctly identifies active statuses', function () {
    expect(SubscriptionStatus::ACTIVE->isActive())->toBeTrue()
        ->and(SubscriptionStatus::TRIAL->isActive())->toBeTrue()
        ->and(SubscriptionStatus::GRACE_PERIOD->isActive())->toBeTrue()
        ->and(SubscriptionStatus::CANCELED->isActive())->toBeFalse()
        ->and(SubscriptionStatus::EXPIRED->isActive())->toBeFalse()
        ->and(SubscriptionStatus::PAUSED->isActive())->toBeFalse();
});

test('it creates correct status from webhook events', function () {
    // Test INITIAL_PURCHASE
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'INITIAL_PURCHASE']))
        ->toBe(SubscriptionStatus::ACTIVE);

    // Test INITIAL_PURCHASE with trial
    expect(SubscriptionStatus::fromWebhookEvent([
        'type' => 'INITIAL_PURCHASE',
        'is_trial_period' => true,
    ]))->toBe(SubscriptionStatus::TRIAL);

    // Test RENEWAL
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'RENEWAL']))
        ->toBe(SubscriptionStatus::ACTIVE);

    // Test CANCELLATION
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'CANCELLATION']))
        ->toBe(SubscriptionStatus::CANCELED);

    // Test UNCANCELLATION
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'UNCANCELLATION']))
        ->toBe(SubscriptionStatus::ACTIVE);

    // Test SUBSCRIPTION_PAUSED
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'SUBSCRIPTION_PAUSED']))
        ->toBe(SubscriptionStatus::PAUSED);

    // Test BILLING_ISSUE
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'BILLING_ISSUE']))
        ->toBe(SubscriptionStatus::GRACE_PERIOD);

    // Test EXPIRATION
    expect(SubscriptionStatus::fromWebhookEvent(['type' => 'EXPIRATION']))
        ->toBe(SubscriptionStatus::EXPIRED);
});

test('it throws exception for unknown webhook event types', function () {
    expect(fn () => SubscriptionStatus::fromWebhookEvent(['type' => 'UNKNOWN']))
        ->toThrow(InvalidArgumentException::class, 'Unknown webhook event type: UNKNOWN');
});
