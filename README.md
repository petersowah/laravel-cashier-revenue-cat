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

You can publish the config file with:

```bash
php artisan vendor:publish --tag=revenuecat-config
```

This will create a `config/cashier-revenue-cat.php` file in your config folder.

### Environment Variables

Add these variables to your `.env` file:

```env
REVENUECAT_API_KEY=your_api_key
REVENUECAT_PROJECT_ID=your_project_id
REVENUECAT_WEBHOOK_SECRET=your_webhook_secret
REVENUECAT_WEBHOOK_ENDPOINT=webhook/revenuecat  # Optional, defaults to 'webhook/revenuecat'
```

### Available Configuration Options

#### API Configuration
```php
'api' => [
    'key' => env('REVENUECAT_API_KEY'),
    'project_id' => env('REVENUECAT_PROJECT_ID'),
    'version' => env('REVENUECAT_API_VERSION', 'v2'),
    'base_url' => env('REVENUECAT_API_BASE_URL', 'https://api.revenuecat.com'),
],
```

#### Webhook Configuration
```php
'webhook' => [
    'secret' => env('REVENUECAT_WEBHOOK_SECRET'),
    'tolerance' => env('REVENUECAT_WEBHOOK_TOLERANCE', 300),
    'endpoint' => env('REVENUECAT_WEBHOOK_ENDPOINT', 'webhook/revenuecat'),
    'allowed_ips' => env('REVENUECAT_WEBHOOK_ALLOWED_IPS', ''),
    'rate_limit' => [
        'enabled' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ENABLED', true),
        'max_attempts' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_ATTEMPTS', 60),
        'decay_minutes' => env('REVENUECAT_WEBHOOK_RATE_LIMIT_DECAY', 1),
    ],
],
```

#### Cache Configuration
```php
'cache' => [
    'enabled' => env('REVENUECAT_CACHE_ENABLED', true),
    'ttl' => env('REVENUECAT_CACHE_TTL', 3600),
    'prefix' => env('REVENUECAT_CACHE_PREFIX', 'revenuecat'),
],
```

#### Logging Configuration
```php
'logging' => [
    'enabled' => env('REVENUECAT_LOGGING_ENABLED', true),
    'channel' => env('REVENUECAT_LOGGING_CHANNEL', 'stack'),
    'level' => env('REVENUECAT_LOGGING_LEVEL', 'debug'),
],
```

#### Error Handling Configuration
```php
'error_handling' => [
    'throw_exceptions' => env('REVENUECAT_THROW_EXCEPTIONS', true),
    'log_errors' => env('REVENUECAT_LOG_ERRORS', true),
    'retry_on_error' => env('REVENUECAT_RETRY_ON_ERROR', true),
    'max_retries' => env('REVENUECAT_MAX_RETRIES', 3),
],
```

#### Other Configuration
```php
'currency' => env('REVENUECAT_CURRENCY', 'USD'),
'model' => [
    'user' => config('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class),
],
```

### Webhook Security

The package includes several security features for webhooks:

1. **Signature Verification**: All webhooks are verified using the `X-RevenueCat-Signature` header
2. **Rate Limiting**: By default, webhooks are limited to 60 requests per minute per IP
3. **IP Whitelisting**: You can restrict webhook access to specific IP addresses
4. **CSRF Protection**: Webhook routes are automatically excluded from CSRF protection

To configure webhook security:

```env
# Rate limiting (default: 60 requests per minute)
REVENUECAT_WEBHOOK_RATE_LIMIT_ATTEMPTS=60
REVENUECAT_WEBHOOK_RATE_LIMIT_DECAY=1

# IP whitelisting (comma-separated list)
REVENUECAT_WEBHOOK_ALLOWED_IPS=1.2.3.4,5.6.7.8

# Disable rate limiting if needed
REVENUECAT_WEBHOOK_RATE_LIMIT_ENABLED=false
```

### Custom Webhook Endpoint

To use a custom webhook endpoint, set the `REVENUECAT_WEBHOOK_ENDPOINT` environment variable:

```env
REVENUECAT_WEBHOOK_ENDPOINT=api/revenuecat/webhook
```

The webhook URL will be: `https://your-domain.com/api/revenuecat/webhook`

### Route Group Configuration

By default, the webhook route is registered in the `web` middleware group. You can change this by setting the `REVENUECAT_ROUTE_GROUP` environment variable:

```env
# For API routes (with api middleware)
REVENUECAT_ROUTE_GROUP=api

# For web routes (with web middleware)
REVENUECAT_ROUTE_GROUP=web
```

This affects which middleware group the webhook route belongs to. The default is `web`.

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

1. The package automatically registers a webhook route at `/webhook/revenuecat`. You can configure the endpoint in your `.env` file:

```env
REVENUE_CAT_WEBHOOK_ENDPOINT=webhook/revenuecat
```

2. Set up the webhook URL in your RevenueCat dashboard:
   - Log in to your RevenueCat dashboard at https://app.revenuecat.com
   - Go to Project Settings (gear icon) in the left sidebar
   - Click on "Webhooks" in the settings menu
   - Click "Add Webhook"
   - Enter your webhook URL (e.g., `https://your-app.com/webhook/revenuecat`)
   - Select the events you want to receive
   - RevenueCat will generate a webhook secret for you

3. Configure your webhook secret in your `.env` file:
```env
REVENUE_CAT_WEBHOOK_SECRET=your_webhook_secret_here
```

The webhook secret is used to verify that incoming webhook requests are actually from RevenueCat. The package uses this secret to verify the `X-RevenueCat-Signature` header in each webhook request.

4. Publish the webhook handler file to customize the webhook handling:
```bash
php artisan cashier-revenue-cat:publish-webhook-handler
```

This will publish the webhook handler to `app/Listeners/HandleRevenueCatWebhook.php`. You can then modify this file to customize how webhook events are handled.

5. The package automatically handles the following webhook events:
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

6. Listen to webhook events in your application:

```php
// In your EventServiceProvider
protected $listen = [
    \PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived::class => [
        \App\Listeners\HandleRevenueCatWebhook::class,
    ],
];
```

7. Create a webhook handler:

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

The webhook endpoint is automatically secured with signature verification using the `X-RevenueCat-Signature` header. The package will verify the signature using your configured webhook secret before processing any webhook events.

## Database Schema

The package includes migrations for the following tables:

- `revenue_cat_customers`: Stores RevenueCat customer information
  - `id`: Primary key
  - `revenuecat_id`: RevenueCat's customer identifier
  - `email`: Customer's email address
  - `display_name`: Customer's display name
  - `phone_number`: Customer's phone number
  - `metadata`: JSON column for additional customer attributes
  - Timestamps

- `revenue_cat_subscriptions`: Stores subscription information
  - `id`: Primary key
  - `customer_id`: Foreign key to revenue_cat_customers
  - `revenuecat_id`: RevenueCat's subscription identifier
  - `name`: Subscription name
  - `product_id`: RevenueCat product identifier
  - `price_id`: RevenueCat price identifier
  - `status`: Subscription status
  - `cancel_at_period_end`: Whether subscription will cancel at period end
  - `canceled_at`: When the subscription was canceled
  - `trial_ends_at`: When the trial period ends
  - `ends_at`: When the subscription ends
  - `last_event_at`: When the last event occurred
  - Timestamps

- `revenue_cat_receipts`: Stores transaction receipts
  - `id`: Primary key
  - `customer_id`: Foreign key to revenue_cat_customers
  - `transaction_id`: RevenueCat transaction identifier
  - `store`: App store identifier (App Store/Play Store)
  - `environment`: Production or sandbox
  - `price`: Transaction amount
  - `currency`: Transaction currency
  - `purchased_at`: When the purchase was made
  - `expires_at`: When the purchase expires
  - Timestamps

## Models

The package includes the following models in the `PeterSowah\LaravelCashierRevenueCat\Models` namespace:

- `Customer`: Represents a RevenueCat customer
- `Subscription`: Represents a customer's subscription
- `Receipt`: Represents a transaction receipt

To use these models in your application, you can either:

1. Use them directly:
```php
use PeterSowah\LaravelCashierRevenueCat\Models\Customer;
use PeterSowah\LaravelCashierRevenueCat\Models\Subscription;
use PeterSowah\LaravelCashierRevenueCat\Models\Receipt;
```

2. Or extend them for custom functionality:
```php
use PeterSowah\LaravelCashierRevenueCat\Models\Customer as RevenueCatCustomer;

class Customer extends RevenueCatCustomer
{
    // Your custom functionality
}
```

## Testing

The package includes a comprehensive test suite. To run the tests:

```bash
composer test
```

This will run both PHPUnit tests and PHPStan static analysis. You can run them separately:

```bash
# Run PHPUnit tests only
vendor/bin/pest

# Run PHPStan analysis only
vendor/bin/phpstan analyse
```

The package maintains a high level of type safety and follows Laravel's coding standards.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email petersowah@gmail.com instead of using the issue tracker.

## Credits

- [Peter Sowah](https://github.com/petersowah)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.