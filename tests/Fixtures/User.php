<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PeterSowah\LaravelCashierRevenueCat\Concerns\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \PeterSowah\LaravelCashierRevenueCat\Models\Customer $customer
 * @property \PeterSowah\LaravelCashierRevenueCat\Models\Subscription $subscriptions
 */
class User extends Authenticatable
{
    use Billable, Notifiable;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the RevenueCat app user identifier.
     */
    public function getRevenueCatAppUserId(): string
    {
        return 'test_user_'.$this->id;
    }

    /**
     * Get the store platform for this user.
     */
    public function getStorePlatform(): ?string
    {
        return 'app_store'; // or 'play_store' for Android
    }

    /**
     * Get the user's attributes for RevenueCat.
     *
     * @return array<string, mixed>
     */
    public function getRevenueCatAttributes(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'platform' => $this->getStorePlatform(),
        ];
    }
}
