<?php

namespace PeterSowah\LaravelCashierRevenueCat\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use PeterSowah\LaravelCashierRevenueCat\Customer;
use PeterSowah\LaravelCashierRevenueCat\Receipt;
use PeterSowah\LaravelCashierRevenueCat\Subscription;

trait Billable
{
    public function customer(): MorphOne
    {
        return $this->morphOne(Customer::class, 'billable');
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'billable')->orderBy('created_at', 'desc');
    }

    public function subscription(?string $name = 'default'): ?Subscription
    {
        return $this->subscriptions()->where('name', $name)->first();
    }

    public function receipts(): MorphMany
    {
        return $this->morphMany(Receipt::class, 'billable')->orderBy('purchased_at', 'desc');
    }

    public function hasRevenueCatId(): bool
    {
        return ! is_null($this->customer?->revenuecat_id);
    }

    public function createAsRevenueCatCustomer(array $attributes = []): Customer
    {
        if ($this->hasRevenueCatId()) {
            return $this->customer;
        }

        return $this->customer()->create($attributes);
    }
}
