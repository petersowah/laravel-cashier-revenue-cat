<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Money\Currency;
use Money\Money;

/**
 * @property string $revenuecat_id
 * @property string $product_id
 * @property string $price_id
 * @property string $store
 * @property string $currency
 * @property int $amount
 * @property \Carbon\Carbon $purchased_at
 */
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
        'amount' => 'integer',
        'currency' => 'string',
        'revenuecat_id' => 'string',
        'product_id' => 'string',
        'price_id' => 'string',
        'store' => 'string',
    ];

    protected $attributes = [
        'amount' => 0,
        'currency' => 'USD',
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
