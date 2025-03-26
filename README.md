# Laravel Cashier RevenueCat

[![Latest Version on Packagist](https://img.shields.io/packagist/v/petersowah/laravel-cashier-revenue-cat.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-cashier-revenue-cat)
[![Total Downloads](https://img.shields.io/packagist/dt/petersowah/laravel-cashier-revenue-cat.svg?style=flat-square)](https://packagist.org/packages/petersowah/laravel-cashier-revenue-cat)

A Laravel Cashier driver for RevenueCat, providing seamless integration with RevenueCat's subscription management platform for iOS and Android apps.

## Features

- Easy integration with RevenueCat's API V2
- Webhook handling for subscription events
- Support for both iOS and Android subscriptions
- Secure webhook signature verification
- Event-driven architecture for subscription management
- Support for entitlements management
- Support for non-subscription purchases

## Installation

You can install the package via composer:

```bash
composer require petersowah/laravel-cashier-revenue-cat
```

## Configuration

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag="cashier-revenue-cat-config"
```

2. Publish the migration files:

```bash
php artisan vendor:publish --tag="cashier-revenue-cat-migrations"
```

3. Run the migrations:

```bash
php artisan migrate
```

4. Configure your environment variables:

Copy the `.env.example` file to `.env` and update the values:

```env
# Required Configuration
REVENUE_CAT_PUBLIC_KEY=your_public_key_here
REVENUE_CAT_SECRET_KEY=your_secret_key_here
REVENUE_CAT_WEBHOOK_SECRET=your_webhook_secret_here

# Optional Configuration
REVENUE_CAT_WEBHOOK_URL=/revenue-cat/webhook
REVENUE_CAT_API_VERSION=v2
REVENUE_CAT_API_BASE_URL=https://api.revenuecat.com
REVENUE_CAT_WEBHOOK_ENABLED=true
REVENUE_CAT_WEBHOOK_QUEUE=default
REVENUE_CAT_CACHE_TTL=3600
REVENUE_CAT_CACHE_ENABLED=true
REVENUE_CAT_LOG_LEVEL=debug
REVENUE_CAT_LOG_ENABLED=true
REVENUE_CAT_THROW_ON_ERROR=true
REVENUE_CAT_RETRY_ON_ERROR=true
REVENUE_CAT_MAX_RETRIES=3
```

### Environment Variables

| Variable | Description | Required | Default |
|----------|-------------|----------|---------|
| `REVENUE_CAT_PUBLIC_KEY` | Your RevenueCat public API key | Yes | - |
| `REVENUE_CAT_SECRET_KEY` | Your RevenueCat secret API key | Yes | - |
| `REVENUE_CAT_WEBHOOK_SECRET` | Secret for webhook signature verification | Yes | - |
| `REVENUE_CAT_WEBHOOK_URL` | URL path for webhook endpoint | No | `/revenue-cat/webhook` |
| `REVENUE_CAT_API_VERSION` | RevenueCat API version | No | `v2` |
| `REVENUE_CAT_API_BASE_URL` | RevenueCat API base URL | No | `https://api.revenuecat.com` |
| `REVENUE_CAT_WEBHOOK_ENABLED` | Enable/disable webhook handling | No | `true` |
| `REVENUE_CAT_WEBHOOK_QUEUE` | Queue for webhook processing | No | `default` |
| `REVENUE_CAT_CACHE_TTL` | Cache time to live in seconds | No | `3600` |
| `REVENUE_CAT_CACHE_ENABLED` | Enable/disable caching | No | `true` |
| `REVENUE_CAT_LOG_LEVEL` | Logging level (debug, info, warning, error) | No | `debug` |
| `REVENUE_CAT_LOG_ENABLED` | Enable/disable logging | No | `true` |
| `REVENUE_CAT_THROW_ON_ERROR` | Throw exceptions on API errors | No | `true` |
| `REVENUE_CAT_RETRY_ON_ERROR` | Retry failed API calls | No | `true` |
| `REVENUE_CAT_MAX_RETRIES` | Maximum number of retries | No | `3` |

5. Update your `User` model to use the RevenueCat Billable trait:

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
Purchases.shared.purchase(package: package) { (transaction, customerInfo, error, userCancelled) in
    if let customerInfo = customerInfo {
        // Purchase successful
        // Check entitlements
        if customerInfo.entitlements["premium"]?.isActive == true {
            // Premium features are active
        }
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
) { customerInfo ->
    // Purchase successful
    // Check entitlements
    if (customerInfo.entitlements["premium"]?.isActive == true) {
        // Premium features are active
    }
}
```

## Flutter Integration

1. Add the RevenueCat Flutter SDK to your `pubspec.yaml`:

```yaml
dependencies:
  purchases_flutter: ^6.0.0  # Use the latest version
```

2. Initialize RevenueCat in your Flutter app:

```dart
import 'package:purchases_flutter/purchases_flutter.dart';

// Initialize RevenueCat (typically in your app initialization)
await Purchases.setLogLevel(LogLevel.debug);
await Purchases.configure(PurchasesConfiguration("your_public_key"));

// When user signs up/logs in
await Purchases.logIn('user_identifier'); // Use your user's unique ID

// Get available packages
try {
  Offerings offerings = await Purchases.getOfferings();
  if (offerings.current != null) {
    // Display packages to user
    List<Package> packages = offerings.current.availablePackages;
  }
} catch (e) {
  // Handle error
}

// When user selects a package to purchase
try {
  CustomerInfo customerInfo = await Purchases.purchasePackage(package);
  if (customerInfo.entitlements.active.containsKey('premium')) {
    // Purchase successful
    // The webhook will handle the rest
  }
} catch (e) {
  // Handle error
}
```

## Laravel Backend Usage

### Managing Subscribers

```php
// Get subscriber information
$subscriber = $user->subscription()->getSubscriber();

// Get subscriber's entitlements
$entitlements = $user->getEntitlements();

// Check if user has specific entitlement
if ($user->hasEntitlement('premium')) {
    // User has premium access
}

// Get current offering
$offering = $user->getCurrentOffering();

// Get subscription history
$history = $user->getSubscriptionHistory();

// Get non-subscription purchases
$purchases = $user->getNonSubscriptions();

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
- Subscription Paused
- Subscription Resumed
- Product Change
- Billing Issue
- Refund
- Non-Renewing Purchase
- Subscription Period Changed

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
    $payload = $event->payload;
    $eventType = $payload['event']['type'];
    $subscriber = $payload['event']['subscriber'];
    $entitlements = $subscriber['entitlements'];

    switch ($eventType) {
        case 'INITIAL_PURCHASE':
            // Handle initial purchase
            break;
        case 'RENEWAL':
            // Handle renewal
            break;
        case 'CANCELLATION':
            // Handle cancellation
            break;
        // ... handle other events
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
