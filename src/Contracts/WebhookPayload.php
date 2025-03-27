<?php

namespace PeterSowah\LaravelCashierRevenueCat\Contracts;

interface WebhookPayload
{
    /**
     * The event type.
     */
    public const EVENT_TYPE = 'event.type';

    /**
     * The event ID.
     */
    public const EVENT_ID = 'event.id';

    /**
     * The event creation timestamp.
     */
    public const EVENT_CREATED_AT = 'event.created_at';

    /**
     * The subscriber ID.
     */
    public const SUBSCRIBER_ID = 'event.subscriber.id';

    /**
     * The subscriber entitlements.
     */
    public const SUBSCRIBER_ENTITLEMENTS = 'event.subscriber.entitlements';

    /**
     * The product ID.
     */
    public const PRODUCT_ID = 'event.product.id';

    /**
     * The product identifier.
     */
    public const PRODUCT_IDENTIFIER = 'event.product.identifier';

    /**
     * The product price.
     */
    public const PRODUCT_PRICE = 'event.product.price';

    /**
     * The product currency.
     */
    public const PRODUCT_CURRENCY = 'event.product.currency';
}
