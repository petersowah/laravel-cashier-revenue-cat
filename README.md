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

## Route Registration

The package automatically registers its routes when installed. However, if you're experiencing issues with routes not being registered, follow these steps:

1. Verify the service provider is registered in `config/app.php`:
```php
'providers' => [
    // ...
    PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider::class,
],
```

2. Clear your route cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

3. Verify the routes are registered:
```bash
php artisan route:list | grep revenuecat
```

You should see the webhook route listed with the name `cashier-revenue-cat.webhook`.

### Common Route Registration Issues

1. **Routes Not Showing Up**
   - Make sure you've published the package's configuration
   - Verify the service provider is registered in `config/app.php`
   - Check that your `.env` file has the required configuration

2. **Webhook Route Not Accessible**
   - Verify the `REVENUECAT_WEBHOOK_ENDPOINT` is set correctly in your `.env`
   - Check that the route group (`web` or `api`) is appropriate for your use case
   - Ensure your web server is configured to handle the route

3. **CSRF Token Issues**
   - The webhook route is automatically excluded from CSRF protection
   - If you're still getting CSRF errors, verify the route is registered in the correct middleware group

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag=cashier-revenue-cat-config
```

This will create a `config/cashier-revenue-cat.php` file in your config folder.

### Environment Variables

Add these variables to your `.env` file:

```env
# API Configuration
REVENUECAT_API_KEY=your_api_key
REVENUECAT_PROJECT_ID=your_project_id
REVENUECAT_API_VERSION=v2  # Optional, defaults to 'v2'
REVENUECAT_API_BASE_URL=https://api.revenuecat.com  # Optional, defaults to 'https://api.revenuecat.com'

# Webhook Configuration
REVENUECAT_WEBHOOK_SECRET=your_webhook_secret_here
REVENUECAT_WEBHOOK_ENDPOINT=webhook/revenuecat
REVENUECAT_WEBHOOK_TOLERANCE=300  # Optional, defaults to 300 seconds
REVENUECAT_ROUTE_GROUP=web  # Optional, defaults to 'web'
REVENUECAT_WEBHOOK_ALLOWED_IPS=  # Optional, comma-separated list of allowed IPs

# Webhook Rate Limiting
REVENUECAT_WEBHOOK_RATE_LIMIT_ENABLED=true  # Optional, defaults to true
REVENUECAT_WEBHOOK_RATE_LIMIT_ATTEMPTS=60  # Optional, defaults to 60 attempts
REVENUECAT_WEBHOOK_RATE_LIMIT_DECAY=1  # Optional, defaults to 1 minute

# Cache Configuration
REVENUECAT_CACHE_ENABLED=true  # Optional, defaults to true
REVENUECAT_CACHE_TTL=3600  # Optional, defaults to 3600 seconds
REVENUECAT_CACHE_PREFIX=revenuecat  # Optional, defaults to 'revenuecat'

# Logging Configuration
REVENUECAT_LOGGING_ENABLED=true  # Optional, defaults to true
REVENUECAT_LOGGING_CHANNEL=stack  # Optional, defaults to 'stack'
REVENUECAT_LOGGING_LEVEL=debug  # Optional, defaults to 'debug'

# Error Handling Configuration
REVENUECAT_THROW_EXCEPTIONS=true  # Optional, defaults to true
REVENUECAT_LOG_ERRORS=true  # Optional, defaults to true
REVENUECAT_RETRY_ON_ERROR=true  # Optional, defaults to true
REVENUECAT_MAX_RETRIES=3  # Optional, defaults to 3

# Other Configuration
REVENUECAT_CURRENCY=USD  # Optional, defaults to 'USD'
```

### Available Configuration Options

The package configuration is organized into several sections:

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
    'route_group' => env('REVENUECAT_ROUTE_GROUP', 'web'),
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

For example, if you set `REVENUECAT_ROUTE_GROUP=api`, the webhook route will be registered as:
```
POST api/webhook/revenuecat
```

If you set `REVENUECAT_ROUTE_GROUP=web`, the webhook route will be registered as:
```
POST webhook/revenuecat
```

The route name will always be `cashier-revenue-cat.webhook` regardless of the group.

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
REVENUECAT_WEBHOOK_ENDPOINT=webhook/revenuecat
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
REVENUECAT_WEBHOOK_SECRET=your_webhook_secret_here
```

4. You have two options for handling webhooks:

   a. Use the default webhook handler (no configuration needed):
   The package will automatically use the built-in webhook handler.

   b. Create your own webhook handler:
   ```bash
   php artisan cashier-revenue-cat:publish-webhook-handler
   ```
   This will:
   - Publish the webhook handler to `app/Listeners/HandleRevenueCatWebhook.php`
   - Publish the webhook controller to `app/Http/Controllers/RevenueCat/WebhookController.php`
   - Update the configuration to use your published controller
   - Update the route registration to use your published controller

   Your custom handler should implement the following interface:
   ```php
   namespace App\Listeners;

   use Illuminate\Http\Request;
   use Illuminate\Http\Response;

   class HandleRevenueCatWebhook
   {
       public function handle(Request $request): Response
       {
           // Your custom webhook handling logic here
           
           return response('', 200);
       }
   }
   ```

   Note: The webhook handler configuration must include both the class name and the method name (e.g., `Class@method`). The method name is required and must be specified after the `@` symbol. The method should accept a `Request` object and return a `Response`.

   After publishing, the webhook route will use your published controller at `App\Http\Controllers\RevenueCat\WebhookController`, which will dispatch the webhook event to your configured handler. You can customize both the controller and handler to implement your specific webhook handling logic.

5. The package automatically handles the following webhook events:
- INITIAL_PURCHASE
- RENEWAL
- CANCELLATION
- NON_RENEWING_PURCHASE
- SUBSCRIPTION_PAUSED
- SUBSCRIPTION_RESUMED
- PRODUCT_CHANGE
- BILLING_ISSUE
- REFUND
- SUBSCRIPTION_PERIOD_CHANGED

6. Listen to webhook events in your application:

```php
// In your EventServiceProvider (app/Providers/EventServiceProvider.php)
protected $listen = [
    \PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived::class => [
        \App\Listeners\HandleRevenueCatWebhook::class,
    ],
];
```

7. The default webhook handler includes comprehensive event handling:

```php
namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['event']['type'];

        Log::info('RevenueCat webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        switch ($type) {
            case 'INITIAL_PURCHASE':
                $this->handleInitialPurchase($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
            case 'NON_RENEWING_PURCHASE':
                $this->handleNonRenewingPurchase($payload);
                break;
            case 'SUBSCRIPTION_PAUSED':
                $this->handleSubscriptionPaused($payload);
                break;
            case 'SUBSCRIPTION_RESUMED':
                $this->handleSubscriptionResumed($payload);
                break;
            case 'PRODUCT_CHANGE':
                $this->handleProductChange($payload);
                break;
            case 'BILLING_ISSUE':
                $this->handleBillingIssue($payload);
                break;
            case 'REFUND':
                $this->handleRefund($payload);
                break;
            case 'SUBSCRIPTION_PERIOD_CHANGED':
                $this->handleSubscriptionPeriodChanged($payload);
                break;
        }
    }

    protected function handleInitialPurchase(array $payload): void
    {
        // Handle initial purchase
        Log::info('Handling initial purchase', ['payload' => $payload]);
    }

    protected function handleRenewal(array $payload): void
    {
        // Handle renewal
        Log::info('Handling renewal', ['payload' => $payload]);
    }

    protected function handleCancellation(array $payload): void
    {
        // Handle cancellation
        Log::info('Handling cancellation', ['payload' => $payload]);
    }

    protected function handleNonRenewingPurchase(array $payload): void
    {
        // Handle non-renewing purchase
        Log::info('Handling non-renewing purchase', ['payload' => $payload]);
    }

    protected function handleSubscriptionPaused(array $payload): void
    {
        // Handle subscription paused
        Log::info('Handling subscription paused', ['payload' => $payload]);
    }

    protected function handleSubscriptionResumed(array $payload): void
    {
        // Handle subscription resumed
        Log::info('Handling subscription resumed', ['payload' => $payload]);
    }

    protected function handleProductChange(array $payload): void
    {
        // Handle product change
        Log::info('Handling product change', ['payload' => $payload]);
    }

    protected function handleBillingIssue(array $payload): void
    {
        // Handle billing issue
        Log::info('Handling billing issue', ['payload' => $payload]);
    }

    protected function handleRefund(array $payload): void
    {
        // Handle refund
        Log::info('Handling refund', ['payload' => $payload]);
    }

    protected function handleSubscriptionPeriodChanged(array $payload): void
    {
        // Handle subscription period changed
        Log::info('Handling subscription period changed', ['payload' => $payload]);
    }
}
```

The webhook endpoint is automatically secured with signature verification using the `X-RevenueCat-Signature` header. The package will verify the signature using your configured webhook secret before processing any webhook events.

### Webhook Event Handling

The package dispatches a `WebhookReceived` event for each webhook request. You can listen to this event in your application by:

1. Registering the event listener in your `EventServiceProvider`:
```php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        WebhookReceived::class => [
            \App\Listeners\HandleRevenueCatWebhook::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
```

2. Creating a listener to handle the event:
```bash
php artisan make:listener HandleRevenueCatWebhook --event=WebhookReceived
```

3. Implementing the event handling logic in your listener:
```php
// app/Listeners/HandleRevenueCatWebhook.php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['event']['type'];

        Log::info('RevenueCat webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        switch ($type) {
            case 'INITIAL_PURCHASE':
                $this->handleInitialPurchase($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
            case 'NON_RENEWING_PURCHASE':
                $this->handleNonRenewingPurchase($payload);
                break;
            case 'SUBSCRIPTION_PAUSED':
                $this->handleSubscriptionPaused($payload);
                break;
            case 'SUBSCRIPTION_RESUMED':
                $this->handleSubscriptionResumed($payload);
                break;
            case 'PRODUCT_CHANGE':
                $this->handleProductChange($payload);
                break;
            case 'BILLING_ISSUE':
                $this->handleBillingIssue($payload);
                break;
            case 'REFUND':
                $this->handleRefund($payload);
                break;
            case 'SUBSCRIPTION_PERIOD_CHANGED':
                $this->handleSubscriptionPeriodChanged($payload);
                break;
        }
    }

    protected function handleInitialPurchase(array $payload): void
    {
        // Handle initial purchase
        Log::info('Handling initial purchase', ['payload' => $payload]);
    }

    protected function handleRenewal(array $payload): void
    {
        // Handle renewal
        Log::info('Handling renewal', ['payload' => $payload]);
    }

    protected function handleCancellation(array $payload): void
    {
        // Handle cancellation
        Log::info('Handling cancellation', ['payload' => $payload]);
    }

    protected function handleNonRenewingPurchase(array $payload): void
    {
        // Handle non-renewing purchase
        Log::info('Handling non-renewing purchase', ['payload' => $payload]);
    }

    protected function handleSubscriptionPaused(array $payload): void
    {
        // Handle subscription paused
        Log::info('Handling subscription paused', ['payload' => $payload]);
    }

    protected function handleSubscriptionResumed(array $payload): void
    {
        // Handle subscription resumed
        Log::info('Handling subscription resumed', ['payload' => $payload]);
    }

    protected function handleProductChange(array $payload): void
    {
        // Handle product change
        Log::info('Handling product change', ['payload' => $payload]);
    }

    protected function handleBillingIssue(array $payload): void
    {
        // Handle billing issue
        Log::info('Handling billing issue', ['payload' => $payload]);
    }

    protected function handleRefund(array $payload): void
    {
        // Handle refund
        Log::info('Handling refund', ['payload' => $payload]);
    }

    protected function handleSubscriptionPeriodChanged(array $payload): void
    {
        // Handle subscription period changed
        Log::info('Handling subscription period changed', ['payload' => $payload]);
    }
}
```

The event payload contains all the information from the RevenueCat webhook, including:
- Event type
- Event ID
- Timestamp
- Subscriber information
- Product information
- Entitlements
- And more

You can access this information in your event listener to implement your business logic.

### Webhook Event Types

The package handles the following webhook event types:

1. **INITIAL_PURCHASE**
   - Triggered when a user makes their first purchase
   - Contains initial subscription details and user information

2. **RENEWAL**
   - Triggered when a subscription is renewed
   - Contains updated subscription period information

3. **CANCELLATION**
   - Triggered when a subscription is cancelled
   - Contains cancellation details and effective date

4. **NON_RENEWING_PURCHASE**
   - Triggered when a subscription is set to not renew
   - Contains information about when the subscription will end

5. **SUBSCRIPTION_PAUSED**
   - Triggered when a subscription is paused
   - Contains pause duration and reason

6. **SUBSCRIPTION_RESUMED**
   - Triggered when a paused subscription is resumed
   - Contains updated subscription status

7. **PRODUCT_CHANGE**
   - Triggered when a subscription product is changed
   - Contains old and new product information

8. **BILLING_ISSUE**
   - Triggered when there's a billing problem
   - Contains error details and resolution steps

9. **REFUND**
   - Triggered when a purchase is refunded
   - Contains refund amount and reason

10. **SUBSCRIPTION_PERIOD_CHANGED**
    - Triggered when a subscription period is modified
    - Contains old and new period information

Each event type provides specific data in the payload that you can use to implement your business logic. The event listener example above shows how to handle each type of event.

## Database Tables

The package creates the following database tables:

- `customers`: Stores customer information
- `subscriptions`: Stores subscription information

## Models

The package provides the following models:

- `Customer`: Represents a customer
- `Subscription`: Represents a subscription

## Usage

To use the package, add the `Billable` trait to your User model:

```php
use PeterSowah\LaravelCashierRevenueCat\Concerns\Billable;

class User extends Authenticatable
{
    use Billable;
    // ...
}
```

This will give your User model access to the following relationships:

- `customer()`: Get the customer associated with the user
- `subscriptions()`: Get the subscriptions associated with the user

## Configuration

The package can be configured by publishing the configuration file:

```bash
php artisan vendor:publish --provider="PeterSowah\LaravelCashierRevenueCat\LaravelCashierRevenueCatServiceProvider" --tag="config"
```

This will create a `config/cashier-revenue-cat.php` file where you can configure:

- API Key
- Project ID
- Webhook Secret
- Webhook Endpoint
- Webhook Handler

## Webhooks

The package provides webhook handling for RevenueCat events. To use webhooks:

1. Configure your webhook endpoint in RevenueCat to point to your application's webhook URL
2. Set the webhook secret in your configuration
3. The package will automatically handle incoming webhooks and dispatch events

## Events

The package dispatches the following events:

- `WebhookReceived`: Dispatched when a webhook is received from RevenueCat

## Testing

The package provides a test case that you can use in your tests:

```php
use PeterSowah\LaravelCashierRevenueCat\Tests\TestCase;

class YourTest extends TestCase
{
    // Your test methods
}
```

This test case provides:

- Database configuration for testing
- RevenueCat configuration for testing
- Helper methods for creating test data

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.