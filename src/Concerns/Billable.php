<?php

namespace PeterSowah\LaravelCashierRevenueCat\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use PeterSowah\LaravelCashierRevenueCat\Customer;
use PeterSowah\LaravelCashierRevenueCat\Receipt;
use PeterSowah\LaravelCashierRevenueCat\RevenueCat;
use PeterSowah\LaravelCashierRevenueCat\Subscription;

/**
 * @property-read Customer|null $customer
 * @property-read string|null $revenuecat_id
 *
 * @method MorphMany subscriptions()
 * @method MorphMany receipts()
 */
trait Billable
{
    /**
     * Get the customer related to the billable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<\PeterSowah\LaravelCashierRevenueCat\Customer, \Illuminate\Database\Eloquent\Model>
     */
    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class, 'billable');
    }

    /**
     * Get all of the subscriptions for the billable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\PeterSowah\LaravelCashierRevenueCat\Subscription, \Illuminate\Database\Eloquent\Model>
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'billable')->orderBy('created_at', 'desc');
    }

    /**
     * Get a subscription instance by name for the billable model.
     */
    public function subscription(?string $name = 'default'): ?Subscription
    {
        /** @var Subscription|null */
        return $this->subscriptions()->where('name', $name)->first();
    }

    /**
     * Get all of the receipts for the billable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\PeterSowah\LaravelCashierRevenueCat\Receipt, \Illuminate\Database\Eloquent\Model>
     */
    public function receipts(): MorphMany
    {
        return $this->morphMany(Receipt::class, 'billable')->orderBy('purchased_at', 'desc');
    }

    /**
     * Determine if the billable model has a RevenueCat ID.
     */
    public function hasRevenueCatId(): bool
    {
        return ! is_null($this->customer?->revenuecat_id);
    }

    /**
     * Create a RevenueCat customer for the billable model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function createAsRevenueCatCustomer(array $attributes = []): Customer
    {
        if ($this->hasRevenueCatId()) {
            /** @var Customer */
            return $this->customer;
        }

        /** @var Customer */
        return $this->customer()->create($attributes);
    }

    /**
     * Get the RevenueCat customer ID for the billable model.
     */
    public function revenueCatId(): ?string
    {
        return $this->customer?->revenuecat_id;
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

        $response = app(RevenueCat::class)->getSubscriberEntitlements($this->revenueCatId());

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

        $response = app(RevenueCat::class)->getSubscriberOffering($this->revenueCatId());

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

        $response = app(RevenueCat::class)->getSubscriberHistory($this->revenueCatId(), $params);

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

        return app(RevenueCat::class)->getNonSubscriptions($this->revenueCatId());
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

        return app(RevenueCat::class)->getOfferings($this->revenueCatId());
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

        return app(RevenueCat::class)->getProducts($this->revenueCatId());
    }
}
