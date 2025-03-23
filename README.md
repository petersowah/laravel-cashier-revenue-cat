# Laravel Cashier RevenueCat

[![Latest Version on Packagist](https://img.shields.io/packagist/v/petersowah/laravel-cashier-revenue-cat.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-cashier-revenue-cat)
[![Total Downloads](https://img.shields.io/packagist/dt/petersowah/laravel-cashier-revenue-cat.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-cashier-revenue-cat)

A Laravel Cashier driver for RevenueCat, providing seamless integration with RevenueCat's subscription management platform for iOS and Android apps.

## Features

- Easy integration with RevenueCat's API
- Webhook handling for subscription events
- Support for both iOS and Android subscriptions
- Secure webhook signature verification
- Event-driven architecture for subscription management

## Installation

You can install the package via composer:

```bash
composer require petersowah/laravel-cashier-revenue-cat
```

## Configuration

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag="cashier-revenuecat-config"
```

2. Add your RevenueCat API keys to your `.env` file:

```env
REVENUE_CAT_PUBLIC_KEY=your_public_key
REVENUE_CAT_SECRET_KEY=your_secret_key
REVENUE_CAT_WEBHOOK_SECRET=your_webhook_secret # Optional, for webhook signature verification
```

3. Update your `User` model to use the RevenueCat Billable trait:

```php
use PeterSowah\LaravelCashierRevenueCat\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

## Mobile App Integration

### iOS Integration

1. First, set up RevenueCat in your iOS app by adding the RevenueCat SDK:

```swift
import RevenueCat

// In your AppDelegate or early in app lifecycle
Purchases.configure(
    withAPIKey: "your_public_key",
    appUserID: "user_identifier" // Use the same identifier you'll use in Laravel
)
```

2. Handle purchases in your iOS app:

```swift
// Get available packages
Purchases.shared.getOfferings { (offerings, error) in
    if let packages = offerings?.current?.availablePackages {
        // Display packages to user
    }
}

// Make a purchase
Purchases.shared.purchase(package: package) { (transaction, purchaserInfo, error, userCancelled) in
    if let purchaserInfo = purchaserInfo {
        // Purchase successful
        // Your Laravel webhook will be notified automatically
    }
}
```

### Android Integration

1. Add the RevenueCat SDK to your Android app:

```kotlin
import com.revenuecat.purchases.Purchases

// In your Application class or early in app lifecycle
Purchases.configure(
    PurchasesConfiguration.Builder(context, "your_public_key")
        .appUserID("user_identifier") // Use the same identifier you'll use in Laravel
        .build()
)
```

2. Handle purchases in your Android app:

```kotlin
// Get available packages
Purchases.sharedInstance.getOfferings({ offerings ->
    offerings.current?.availablePackages?.let { packages ->
        // Display packages to user
    }
})

// Make a purchase
Purchases.sharedInstance.purchasePackage(
    activity,
    package
) { purchaseResult ->
    // Purchase successful
    // Your Laravel webhook will be notified automatically
}
```

## Laravel Backend Usage

### Managing Subscribers

```php
// Get subscriber information
$user->subscription()->getSubscriber();

// Create a subscriber
$user->subscription()->createSubscriber([
    'attributes' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]
]);

// Get available offerings
$offerings = $user->subscription()->getOfferings();

// Get available products
$products = $user->subscription()->getProducts();
```

### Handling Webhooks

1. Set up the webhook URL in your RevenueCat dashboard:
```
https://your-app.com/revenue-cat/webhook
```

2. The package automatically handles the following webhook events:
- Initial Purchase
- Renewal
- Cancellation
- Expiration
- Product Change
- Refund

3. Listen to webhook events in your application:

```php
// In your EventServiceProvider
protected $listen = [
    'PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived' => [
        'App\Listeners\HandleRevenueCatWebhook',
    ],
];
```

4. Handle the events in your listener:

```php
public function handle(WebhookReceived $event)
{
    if ($event->payload['type'] === 'INITIAL_PURCHASE') {
        // Handle initial purchase
    }
}
```

## Testing

```bash
composer test
```

## Security

If you discover any security-related issues, please email [security contact] instead of using the issue tracker.

## Credits

- [Peter Sowah](https://github.com/petersowah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
