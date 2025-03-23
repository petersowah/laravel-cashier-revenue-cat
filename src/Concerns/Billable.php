<?php

namespace PeterSowah\LaravelCashierRevenueCat\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use PeterSowah\LaravelCashierRevenueCat\Customer;
use PeterSowah\LaravelCashierRevenueCat\Receipt;
use PeterSowah\LaravelCashierRevenueCat\Subscription;

/**
 * @property-read \PeterSowah\LaravelCashierRevenueCat\Customer|null $customer
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
     *
     * @param string|null $name
     * @return \PeterSowah\LaravelCashierRevenueCat\Subscription|null
     */
    public function subscription(?string $name = 'default'): ?Subscription
    {
        /** @var \PeterSowah\LaravelCashierRevenueCat\Subscription|null */
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
     *
     * @return bool
     */
    public function hasRevenueCatId(): bool
    {
        return ! is_null($this->customer?->revenuecat_id);
    }

    /**
     * Create a RevenueCat customer for the billable model.
     *
     * @param array<string, mixed> $attributes
     * @return \PeterSowah\LaravelCashierRevenueCat\Customer
     */
    public function createAsRevenueCatCustomer(array $attributes = []): Customer
    {
        if ($this->hasRevenueCatId()) {
            /** @var \PeterSowah\LaravelCashierRevenueCat\Customer */
            return $this->customer;
        }

        /** @var \PeterSowah\LaravelCashierRevenueCat\Customer */
        return $this->customer()->create($attributes);
    }

    /**
     * Get the RevenueCat customer ID for the billable model.
     *
     * @return string|null
     */
    public function revenueCatId(): ?string
    {
        return $this->customer?->revenuecat_id;
    }
}
