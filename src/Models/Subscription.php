<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Money\Currency;
use Money\Money;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string $product_id
 * @property string $price_id
 * @property int $quantity
 * @property string $status
 * @property string $currency
 * @property array|null $latest_invoice
 * @property array|null $metadata
 * @property string|null $revenuecat_id
 * @property \Carbon\Carbon|null $trial_ends_at
 * @property \Carbon\Carbon|null $ends_at
 * @property \Carbon\Carbon|null $canceled_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'customer_id',
        'name',
        'product_id',
        'price_id',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'canceled_at',
        'metadata',
        'currency',
        'status',
        'latest_invoice',
        'revenuecat_id',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'metadata' => 'array',
        'latest_invoice' => 'array',
    ];

    protected $attributes = [
        'status' => 'active',
        'trial_ends_at' => null,
        'ends_at' => null,
        'canceled_at' => null,
        'quantity' => 1,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function active(): bool
    {
        return is_null($this->ends_at) || $this->onGracePeriod();
    }

    public function cancelled(): bool
    {
        return ! is_null($this->ends_at);
    }

    public function ended(): bool
    {
        return $this->cancelled() && $this->ends_at->isPast();
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function recurring(): bool
    {
        return ! $this->cancelled() || $this->onTrial();
    }

    public function onGracePeriod(): bool
    {
        return $this->canceled_at && $this->ends_at && $this->ends_at->isFuture();
    }

    public function canceled(): bool
    {
        return $this->canceled_at !== null;
    }

    public function hasIncompletePayment(): bool
    {
        return $this->status === 'incomplete';
    }

    public function incompletePayment(): ?array
    {
        if (! $this->hasIncompletePayment()) {
            return null;
        }

        $paymentIntent = $this->latest_invoice['payment_intent'] ?? null;

        return [
            'payment_intent' => $paymentIntent,
            'subscription' => $this->revenuecat_id,
        ];
    }

    public function cancel(?Carbon $at = null): void
    {
        $this->fill([
            'canceled_at' => $at ?? now(),
        ])->save();
    }

    public function resume(): void
    {
        $this->fill([
            'canceled_at' => null,
        ])->save();
    }

    public function incrementQuantity(int $count = 1): void
    {
        $this->updateQuantity($this->quantity + $count);
    }

    public function decrementQuantity(int $count = 1): void
    {
        $this->updateQuantity($this->quantity - $count);
    }

    public function updateQuantity(int $quantity): void
    {
        $this->fill([
            'quantity' => $quantity,
        ])->save();
    }

    public function swap(string $priceId): void
    {
        $this->fill([
            'price_id' => $priceId,
        ])->save();
    }

    public function updateQuantityForPrice(string $priceId, int $quantity): void
    {
        $this->items()->where('price_id', $priceId)->update([
            'quantity' => $quantity,
        ]);
    }

    public function prorate(): void
    {
        // RevenueCat handles proration automatically
    }

    public function skipTrial(): void
    {
        $this->fill([
            'trial_ends_at' => null,
        ])->save();
    }

    public function extendTrial(Carbon $date): void
    {
        $this->fill([
            'trial_ends_at' => $date,
        ])->save();
    }

    public function getMoneyAmount(string $field): Money
    {
        return new Money($this->{$field}, new Currency($this->currency));
    }
}
