<?php

namespace PeterSowah\LaravelCashierRevenueCat\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Notifications\Notifiable;
use PeterSowah\LaravelCashierRevenueCat\Concerns\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \PeterSowah\LaravelCashierRevenueCat\Customer|null $customer
 */
class User extends Model
{
    use Billable, Notifiable;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Get the RevenueCat app user identifier.
     *
     * @return string
     */
    public function getRevenueCatAppUserId(): string
    {
        return 'test_user_' . $this->id;
    }

    /**
     * Get the store platform for this user.
     *
     * @return string|null
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