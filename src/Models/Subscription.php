<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;
use PeterSowah\LaravelCashierRevenueCat\Enums\SubscriptionStatus;

/**
 * @property string $name
 * @property string $revenuecat_id
 * @property SubscriptionStatus $status
 * @property string $price
 * @property string $product_id
 * @property Carbon|null $current_period_started_at
 * @property Carbon|null $current_period_ended_at
 * @property Carbon|null $trial_started_at
 * @property Carbon|null $trial_ended_at
 * @property Carbon|null $canceled_at
 * @property Carbon|null $ended_at
 * @property Carbon|null $paused_at
 * @property Carbon|null $resumed_at
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'revenuecat_id',
        'status',
        'price',
        'product_id',
        'current_period_started_at',
        'current_period_ended_at',
        'trial_started_at',
        'trial_ended_at',
        'canceled_at',
        'ended_at',
        'paused_at',
        'resumed_at',
        'metadata',
        'store'
    ];

    protected $casts = [
        'current_period_started_at' => 'datetime',
        'current_period_ended_at' => 'datetime',
        'trial_started_at' => 'datetime',
        'trial_ended_at' => 'datetime',
        'canceled_at' => 'datetime',
        'ended_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'metadata' => 'array',
        'status' => SubscriptionStatus::class,
    ];

    protected $attributes = [
        'status' => SubscriptionStatus::ACTIVE,
    ];

    /**
     * Get the subscription's billable model.
     */
    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function active(): bool
    {
        return $this->status->isActive() && ! $this->ended();
    }

    public function cancelled(): bool
    {
        return ! is_null($this->canceled_at);
    }

    public function ended(): bool
    {
        return $this->cancelled() && $this->ended_at?->isPast();
    }

    public function onTrial(): bool
    {
        return $this->trial_ended_at && $this->trial_ended_at->isFuture();
    }

    public function recurring(): bool
    {
        return ! $this->cancelled() || $this->onTrial();
    }

    public function onGracePeriod(): bool
    {
        return $this->cancelled() && $this->ended_at?->isFuture();
    }

    public function cancel(?DateTimeInterface $endsAt = null): self
    {
        $endsAt = $endsAt ? Carbon::instance($endsAt) : Carbon::now();

        $this->canceled_at = Carbon::now();
        $this->ended_at = Carbon::instance($endsAt);
        $this->status = SubscriptionStatus::CANCELED;
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

        $this->canceled_at = null;
        $this->ended_at = null;
        $this->resumed_at = Carbon::now();
        $this->status = SubscriptionStatus::ACTIVE;
        $this->save();

        return $this;
    }

    public function pause(): self
    {
        $this->paused_at = Carbon::now();
        $this->status = SubscriptionStatus::PAUSED;
        $this->save();

        return $this;
    }

    public function markAsExpired(): self
    {
        $this->status = SubscriptionStatus::EXPIRED;
        $this->save();

        return $this;
    }

    public function markAsGracePeriod(): self
    {
        $this->status = SubscriptionStatus::GRACE_PERIOD;
        $this->save();

        return $this;
    }
}
