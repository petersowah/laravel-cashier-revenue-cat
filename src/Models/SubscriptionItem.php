<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $subscription_id
 * @property string $product_id
 * @property string $price_id
 * @property int $quantity
 */
class SubscriptionItem extends Model
{
    protected $table = 'subscription_items';

    protected $fillable = [
        'subscription_id',
        'product_id',
        'price_id',
        'quantity',
        'metadata',
    ];

    protected $casts = [
        'subscription_id' => 'integer',
        'product_id' => 'string',
        'price_id' => 'string',
        'quantity' => 'integer',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'quantity' => 1,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function updateQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        $this->save();

        return $this;
    }

    public function incrementQuantity(int $amount = 1): self
    {
        return $this->updateQuantity($this->quantity + $amount);
    }

    public function decrementQuantity(int $amount = 1): self
    {
        return $this->updateQuantity($this->quantity - $amount);
    }
}
