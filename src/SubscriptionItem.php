<?php

namespace PeterSowah\LaravelCashierRevenueCat;

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
    ];

    protected $casts = [
        'quantity' => 'integer',
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
