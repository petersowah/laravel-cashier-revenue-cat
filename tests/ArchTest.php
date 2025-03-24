<?php

test('package class exists', function () {
    expect(class_exists('PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider'))->toBeTrue();
});
arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();
