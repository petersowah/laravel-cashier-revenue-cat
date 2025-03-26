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
REVENUE_CAT_SECRET_KEY=your_secret_key_here
REVENUE_CAT_PROJECT_ID=your_project_id_here
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
| `REVENUE_CAT_SECRET_KEY` | Your RevenueCat secret key for backend operations | Yes | - |
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
    'key' => env('REVENUE_CAT_SECRET_KEY'),
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

```