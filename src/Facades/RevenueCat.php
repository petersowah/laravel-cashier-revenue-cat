<?php

namespace PeterSowah\LaravelCashierRevenueCat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getCustomer(string $appUserId)
 * @method static array createCustomer(string $appUserId, array $attributes = [])
 * @method static array updateCustomer(string $appUserId, array $attributes)
 * @method static array deleteCustomer(string $appUserId)
 * @method static array getOfferings(?string $appUserId = null)
 * @method static array getProducts()
 * @method static array getCustomerHistory(string $appUserId, array $params = [])
 * @method static array getCustomerEntitlements(string $appUserId)
 * @method static array getCustomerPurchases(string $appUserId)
 * @method static array getUserSubscriptions(string $appUserId)
 * @method static array getCustomerOffering(string $appUserId)
 * @method static array getCustomerNonSubscriptions(string $appUserId)
 * @method static array getCustomerSubscriptions(string $appUserId)
 * @method static string getSubscriptionName(array $entitlement)
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
