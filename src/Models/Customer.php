<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PeterSowah\LaravelCashierRevenueCat\Concerns\Billable;

/**
 * @property string $revenuecat_id
 * @property string $store
 * @property-read Model|null $billable
 */
class Customer extends Model
{
    use Billable;

    protected $table = 'customers';

    protected $fillable = [
        'revenuecat_id',
        'email',
        'display_name',
        'phone_number',
        'metadata',
    ];

    protected $casts = [
        'revenuecat_id' => 'string',
        'store' => 'string',
        'metadata' => 'array',
    ];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Collection<int, Subscription>|null
     */
    public function subscriptions()
    {
        /** @var Model|null $billable */
        $billable = $this->billable;
        if (! $billable || ! method_exists($billable, 'subscriptions')) {
            return null;
        }

        return $billable->subscriptions()->get();
    }

    /**
     * @return Collection<int, Receipt>|null
     */
    public function receipts()
    {
        /** @var Model|null $billable */
        $billable = $this->billable;
        if (! $billable || ! method_exists($billable, 'receipts')) {
            return null;
        }

        return $billable->receipts()->get();
    }
}
