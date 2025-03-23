<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $revenuecat_id
 * @property string $store
 */
class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'revenuecat_id',
        'store',
    ];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function subscriptions()
    {
        return $this->billable->subscriptions();
    }

    /**
     * @return Collection<int, Receipt>
     */
    public function receipts()
    {
        return $this->billable->receipts();
    }
}
