<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'transaction_id',
        'store',
        'environment',
        'price',
        'currency',
        'purchase_date',
        'expiration_date',
        'metadata',
    ];

    protected $casts = [
        'price' => 'integer',
        'purchase_date' => 'datetime',
        'expiration_date' => 'datetime',
        'metadata' => 'array',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
