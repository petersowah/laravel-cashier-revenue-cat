<?php

namespace PeterSowah\LaravelCashierRevenueCat\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use PeterSowah\LaravelCashierRevenueCat\Enums\SubscriptionStatus;
use PeterSowah\LaravelCashierRevenueCat\Models\Customer;
use PeterSowah\LaravelCashierRevenueCat\Models\Subscription;
use PeterSowah\LaravelCashierRevenueCat\RevenueCat;

/**
 * @property-read Customer|null $customer
 * @property-read string|null $revenuecat_id
 */
trait Billable
{
    /**
     * Get the customer associated with the billable model.
     */
    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class, 'billable');
    }

    /**
     * Get the subscriptions associated with the billable model.
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'billable');
    }

    /**
     * Get the subscription associated with the billable model.
     */
    public function subscription(): ?Subscription
    {
        /** @var Subscription|null */
        return $this->subscriptions()->first();
    }

    /**
     * Create a customer for the billable model.
     */
    public function createAsRevenueCatCustomer(): Customer
    {
        /** @var Customer $customer */
        $customer = $this->customer()->create([
            'revenuecat_id' => $this->getRevenueCatId(),
        ]);

        return $customer;
    }

    /**
     * Get the RevenueCat ID for the billable model.
     */
    public function getRevenueCatId(): string
    {
        /** @var Customer $customer */
        $customer = $this->customer;

        return $customer->revenuecat_id;
    }

    /**
     * Get the RevenueCat ID for the billable model.
     */
    public function getRevenueCatCustomerId(): string
    {
        /** @var Customer $customer */
        $customer = $this->customer;

        return $customer->revenuecat_id;
    }

    /**
     * Get all active entitlements for the billable model.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getEntitlements(): array
    {
        if (! $this->hasRevenueCatId()) {
            return [];
        }

        $response = app(RevenueCat::class)->getSubscriberEntitlements($this->getRevenueCatId());

        return $response['entitlements'] ?? [];
    }

    /**
     * Get a specific entitlement for the billable model.
     */
    public function getEntitlement(string $identifier): ?array
    {
        return $this->getEntitlements()[$identifier] ?? null;
    }

    /**
     * Check if the billable model has an active entitlement.
     */
    public function hasEntitlement(string $identifier): bool
    {
        $entitlement = $this->getEntitlement($identifier);

        return $entitlement !== null && ($entitlement['is_active'] ?? false);
    }

    /**
     * Get the current offering for the billable model.
     *
     * @return array<string, mixed>|null
     */
    public function getCurrentOffering(): ?array
    {
        if (! $this->hasRevenueCatId()) {
            return null;
        }

        $response = app(RevenueCat::class)->getSubscriberOffering($this->getRevenueCatId());

        $currentOfferingId = $response['current_offering_id'] ?? null;

        if (! $currentOfferingId) {
            return null;
        }

        foreach ($response['offerings'] ?? [] as $offering) {
            if ($offering['identifier'] === $currentOfferingId) {
                return $offering;
            }
        }

        return null;
    }

    /**
     * Get the subscription history for the billable model.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function getSubscriptionHistory(array $params = []): array
    {
        if (! $this->hasRevenueCatId()) {
            return [];
        }

        $response = app(RevenueCat::class)->getSubscriberHistory($this->getRevenueCatId(), $params);

        return $response['transactions'] ?? [];
    }

    /**
     * Get the non-subscription purchases for the billable model.
     *
     * @return array<string, mixed>
     */
    public function getNonSubscriptions(): array
    {
        if (! $this->hasRevenueCatId()) {
            return [];
        }

        return app(RevenueCat::class)->getNonSubscriptions($this->getRevenueCatId());
    }

    /**
     * Get available offerings for the billable model.
     *
     * @return array<string, mixed>
     */
    public function getOfferings(): array
    {
        if (! $this->hasRevenueCatId()) {
            return [];
        }

        return app(RevenueCat::class)->getOfferings($this->getRevenueCatId());
    }

    /**
     * Get available products for the billable model.
     *
     * @return array<string, mixed>
     */
    public function getProducts(): array
    {
        if (! $this->hasRevenueCatId()) {
            return [];
        }

        return app(RevenueCat::class)->getProducts($this->getRevenueCatId());
    }

    /**
     * Determine if the billable model has a RevenueCat ID.
     */
    public function hasRevenueCatId(): bool
    {
        /** @var Customer|null $customer */
        $customer = $this->customer;

        return ! is_null($customer?->revenuecat_id);
    }

    /**
     * Get the active subscription for the billable model.
     */
    public function getActiveSubscription(): ?Subscription
    {
        /** @var Subscription|null */
        return $this->subscriptions()
            ->where('status', SubscriptionStatus::ACTIVE)
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->getActiveSubscription() !== null;
    }
}
