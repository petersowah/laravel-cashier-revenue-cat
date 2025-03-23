<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Money\Currency;
use Money\Money;

class Receipt extends Model
{
    protected $table = 'receipts';

    protected $fillable = [
        'revenuecat_id',
        'product_id',
        'price_id',
        'store',
        'currency',
        'amount',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function amount(): Money
    {
        return new Money($this->amount, new Currency($this->currency));
    }
} 