<?php

namespace PeterSowah\LaravelCashierRevenueCat\Models;

use Illuminate\Database\Eloquent\Model;
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

    /**
     * Get the customer's billable model.
     */
    public function billable()
    {
        return $this->morphTo();
    }

    /**
     * Get the customer's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // Ensure only one customer per billable model
            if (static::where('billable_type', $customer->billable_type)
                ->where('billable_id', $customer->billable_id)
                ->exists()) {
                throw new \RuntimeException('A customer already exists for this billable model.');
            }
        });
    }
}
