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
- Caching support for API responses
- Comprehensive logging and error handling
- Automatic retry mechanism for failed API calls

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
| `REVENUE_CAT_API_KEY` | Your RevenueCat API key | Yes | - |
| `REVENUE_CAT_PROJECT_ID` | Your RevenueCat project ID | Yes | - |
| `REVENUE_CAT_WEBHOOK_SECRET` | Secret for webhook signature verification | Yes | - |
| `REVENUE_CAT_API_VERSION` | RevenueCat API version | No | `v2` |
| `REVENUE_CAT_API_BASE_URL` | RevenueCat API base URL | No | `https://api.revenuecat.com` |
| `REVENUE_CAT_WEBHOOK_TOLERANCE` | Webhook signature tolerance in seconds | No | `300` |
| `REVENUE_CAT_WEBHOOK_ENDPOINT` | Webhook endpoint path | No | `webhook/revenuecat` |
| `REVENUE_CAT_CACHE_ENABLED` | Enable/disable API response caching | No | `true` |
| `REVENUE_CAT_CACHE_TTL` | Cache time to live in seconds | No | `3600` |
| `REVENUE_CAT_CACHE_PREFIX` | Cache key prefix | No | `revenuecat` |
| `REVENUE_CAT_LOGGING_ENABLED` | Enable/disable logging | No | `true` |
| `REVENUE_CAT_LOGGING_CHANNEL` | Logging channel | No | `stack` |
| `REVENUE_CAT_LOGGING_LEVEL` | Logging level | No | `debug` |
| `REVENUE_CAT_THROW_EXCEPTIONS` | Throw exceptions on API errors | No | `true` |
| `REVENUE_CAT_LOG_ERRORS` | Log API errors | No | `true` |
| `REVENUE_CAT_RETRY_ON_ERROR` | Retry failed API calls | No | `true` |
| `REVENUE_CAT_MAX_RETRIES` | Maximum number of retries | No | `3` |
| `REVENUE_CAT_CURRENCY` | Default currency code | No | `USD` |

### Service Configuration

You can also configure the RevenueCat service in `config/services.php`:

```php
'revenuecat' => [
    'key' => env('REVENUE_CAT_API_KEY'),
    'project_id' => env('REVENUE_CAT_PROJECT_ID'),
    'version' => env('REVENUE_CAT_API_VERSION', 'v2'),
    'base_url' => env('REVENUE_CAT_API_BASE_URL', 'https://api.revenuecat.com'),
    'webhook_secret' => env('REVENUE_CAT_WEBHOOK_SECRET'),
    'webhook_tolerance' => env('REVENUE_CAT_WEBHOOK_TOLERANCE', 300),
    'webhook_endpoint' => env('REVENUE_CAT_WEBHOOK_ENDPOINT', 'webhook/revenuecat'),
    'cache_enabled' => env('REVENUE_CAT_CACHE_ENABLED', true),
    'cache_ttl' => env('REVENUE_CAT_CACHE_TTL', 3600),
    'cache_prefix' => env('REVENUE_CAT_CACHE_PREFIX', 'revenuecat'),
    'logging_enabled' => env('REVENUE_CAT_LOGGING_ENABLED', true),
    'logging_channel' => env('REVENUE_CAT_LOGGING_CHANNEL', 'stack'),
    'logging_level' => env('REVENUE_CAT_LOGGING_LEVEL', 'debug'),
    'throw_exceptions' => env('REVENUE_CAT_THROW_EXCEPTIONS', true),
    'log_errors' => env('REVENUE_CAT_LOG_ERRORS', true),
    'retry_on_error' => env('REVENUE_CAT_RETRY_ON_ERROR', true),
    'max_retries' => env('REVENUE_CAT_MAX_RETRIES', 3),
    'currency' => env('REVENUE_CAT_CURRENCY', 'USD'),
],
```

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
    \PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived::class => [
        \App\Listeners\HandleRevenueCatWebhook::class,
    ],
];
```

4. Create a webhook handler:

```php
namespace App\Listeners;

use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['event']['type'];

        switch ($type) {
            case 'INITIAL_PURCHASE':
                // Handle initial purchase
                break;
            case 'RENEWAL':
                // Handle renewal
                break;
            case 'CANCELLATION':
                // Handle cancellation
                break;
            // ... handle other event types
        }
    }
}
```

## Testing

The package includes a comprehensive test suite. To run the tests:

```bash
vendor/bin/pest
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email petersowah@gmail.com instead of using the issue tracker.

## Credits

- [Peter Sowah](https://github.com/petersowah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
