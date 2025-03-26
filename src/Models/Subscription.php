<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * @property string $name
 * @property string $revenuecat_id
 * @property string $status
 * @property string $price_id
 * @property string $product_id
 * @property \Carbon\Carbon|null $trial_ends_at
 * @property \Carbon\Carbon|null $ends_at
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'revenuecat_id',
        'status',
        'price_id',
        'product_id',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => 'string',
        'name' => 'string',
        'revenuecat_id' => 'string',
        'price_id' => 'string',
        'product_id' => 'string',
    ];

    protected $attributes = [
        'status' => 'active',
        'trial_ends_at' => null,
        'ends_at' => null,
    ];

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
        return $this->status === 'active' && ! $this->ended();
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
        return $this->cancelled() && $this->ends_at->isFuture();
    }

    public function cancel(?DateTimeInterface $endsAt = null): self
    {
        $endsAt = $endsAt ? Carbon::instance($endsAt) : Carbon::now();

        $this->ends_at = Carbon::instance($endsAt);
        $this->save();

        return $this;
    }

    public function cancelNow(): self
    {
        return $this->cancel(Carbon::now());
    }

    public function resume(): self
    {
        if (! $this->onGracePeriod()) {
            throw new LogicException('Unable to resume subscription that is not within grace period.');
        }

        $this->ends_at = null;
        $this->save();

        return $this;
    }
}
