<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        $endsAt = $endsAt ?: Carbon::now();

        $this->ends_at = $endsAt;
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