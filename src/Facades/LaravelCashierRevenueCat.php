<?php

namespace PeterSowah\LaravelCashierRevenueCat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCat
 */
class LaravelCashierRevenueCat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCat::class;
    }
}
