<?php

namespace PeterSowah\LaravelCashierRevenueCat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getSubscriber(string $appUserId)
 * @method static array createSubscriber(string $appUserId, array $attributes = [])
 * @method static array updateSubscriber(string $appUserId, array $attributes)
 * @method static array deleteSubscriber(string $appUserId)
 * @method static array getOfferings(?string $appUserId = null)
 * @method static array getProducts()
 * @method static array getSubscriberHistory(string $appUserId, array $params = [])
 * @method static array getSubscriberEntitlements(string $appUserId)
 * @method static array getSubscriberPurchases(string $appUserId)
 * @method static array getUserSubscriptions(string $appUserId)
 * @method static array getSubscriberOffering(string $appUserId)
 * @method static array getSubscriberNonSubscriptions(string $appUserId)
 * @method static array getSubscriberSubscriptions(string $appUserId)
 * @method static \GuzzleHttp\Client setClient(\GuzzleHttp\Client $client)
 *
 * @see \PeterSowah\LaravelCashierRevenueCat\RevenueCat
 */
class RevenueCat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PeterSowah\LaravelCashierRevenueCat\RevenueCat::class;
    }
} 