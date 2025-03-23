<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function subscriptions()
    {
        return $this->billable->subscriptions();
    }

    public function receipts()
    {
        return $this->billable->receipts();
    }
}
