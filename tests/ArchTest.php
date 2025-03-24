<?php

test('package class exists', function () {
    expect(class_exists('PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider'))->toBeTrue();
});
