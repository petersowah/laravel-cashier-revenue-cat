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
  if (customerInfo.entitlements.active.containsKey('your_entitlement_id')) {
    // Purchase successful
    // The webhook will handle the rest
  }
} catch (e) {
  // Handle error
}
```

## Complete Example Scenario

Here's a complete example of implementing subscriptions in a Flutter mobile app with Laravel backend:

### 1. Backend Setup

1. Install the package:
```bash
composer require petersowah/laravel-cashier-revenue-cat
```

2. Configure environment variables:
```env
REVENUECAT_API_KEY=your_secret_key
REVENUECAT_WEBHOOK_SECRET=your_webhook_secret
```

3. Set up your User model:
```php
use PeterSowah\LaravelCashierRevenueCat\Concerns\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

### 2. Flutter Implementation

1. Initialize RevenueCat in your app:
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Purchases.setLogLevel(LogLevel.debug);
  await Purchases.configure(PurchasesConfiguration("your_public_key"));
  runApp(MyApp());
}
```

2. Create a subscription service:
```dart
class SubscriptionService {
  Future<void> login(String userId) async {
    await Purchases.logIn(userId);
  }

  Future<List<Package>> getPackages() async {
    try {
      Offerings offerings = await Purchases.getOfferings();
      return offerings.current?.availablePackages ?? [];
    } catch (e) {
      print('Error getting packages: $e');
      return [];
    }
  }

  Future<bool> purchasePackage(Package package) async {
    try {
      CustomerInfo customerInfo = await Purchases.purchasePackage(package);
      return customerInfo.entitlements.active.isNotEmpty;
    } catch (e) {
      print('Error purchasing package: $e');
      return false;
    }
  }
}
```

### 3. Backend Webhook Handling

1. Create a webhook listener:
```php
class HandleRevenueCatWebhook
{
    public function handle(WebhookReceived $event)
    {
        $payload = $event->payload;
        
        switch ($payload['type']) {
            case 'INITIAL_PURCHASE':
                $this->handleInitialPurchase($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
        }
    }

    private function handleInitialPurchase(array $payload)
    {
        $user = User::where('revenuecat_id', $payload['app_user_id'])->first();
        if ($user) {
            // Update user's subscription status
            $user->subscription()->create([
                'name' => 'default',
                'revenuecat_id' => $payload['subscription_id'],
                'status' => 'active',
                'price_id' => $payload['price_id'],
                'product_id' => $payload['product_id']
            ]);
        }
    }
}
```

### 4. Checking Subscription Status

In your Laravel backend:
```php
// Check active subscription
if ($user->subscription()->active()) {
    // Grant access to premium features
}

// Check trial status
if ($user->subscription()->onTrial()) {
    // Handle trial-specific logic
}

// Get all user's subscriptions
$subscriptions = $user->subscriptions;

// Get purchase receipts
$receipts = $user->receipts;
```

In your Flutter app:
```dart
Future<bool> checkSubscriptionStatus() async {
  try {
    CustomerInfo customerInfo = await Purchases.getCustomerInfo();
    return customerInfo.entitlements.active.containsKey('your_entitlement_id');
  } catch (e) {
    print('Error checking subscription status: $e');
    return false;
  }
}
```

### 5. Error Handling

The package includes built-in exception handling:
```php
try {
    // Your RevenueCat operations
} catch (RevenueCatException $e) {
    // Handle API errors
} catch (WebhookSignatureException $e) {
    // Handle webhook signature verification failures
}
```

This complete example demonstrates:
- Flutter app integration
- Laravel backend setup
- Webhook handling
- Subscription status management
- Error handling
- Security best practices

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
