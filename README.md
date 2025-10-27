# Laravel Pixel Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ideacrafters/laravel-pixels-manager.svg?style=flat-square)](https://packagist.org/packages/ideacrafters/laravel-pixels-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ideacrafters/laravel-pixels-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ideacrafters/laravel-pixels-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ideacrafters/laravel-pixels-manager.svg?style=flat-square)](https://packagist.org/packages/ideacrafters/laravel-pixels-manager)

A unified package for managing multiple advertising platform pixels (Facebook, TikTok, Snapchat) in Laravel applications with server-side conversion tracking.

## Features

- 📊 **Multi-Platform Support**: Built-in support for Facebook, TikTok, and Snapchat
- 🎯 **Selective Targeting**: Target specific platforms or pixels for each event
- 🔄 **Queue-Based Processing**: Zero performance impact with async event processing
- 🔒 **Secure Credentials**: AES-256 encryption for access tokens
- 🛡️ **GDPR/CCPA Compliant**: Built-in consent management
- 📝 **Blade Directives**: Easy-to-use directives for script injection
- ⚡ **Smart Deduplication**: Prevent duplicate events with cache-based deduplication

## Installation

You can install the package via composer:

```bash
composer require ideacrafters/laravel-pixels-manager
```

Run the installation wizard:

```bash
php artisan pixel:install
```

Add your first pixel:

```bash
php artisan pixel:add
```

## Usage

### Basic Tracking

Track events across all configured platforms:

```php
use Ideacrafters\PixelManager\Facades\PixelManager;

PixelManager::track('Purchase', [
    'value' => 99.99,
    'currency' => 'USD',
]);
```

### Selective Tracking

Track to specific platforms:

```php
PixelManager::forPlatforms('facebook', 'tiktok')
    ->track('ViewContent', [
        'content_id' => 'SKU123',
        'value' => 29.99,
    ]);
```

Track to specific pixels:

```php
PixelManager::forPixels(1, 3)
    ->track('AddToCart', [
        'content_id' => 'SKU123',
        'value' => 29.99,
    ]);
```

Exclude platforms:

```php
PixelManager::except('snapchat')
    ->track('PageView');
```

### Blade Directives

Add to your layout's `<head>` section:

```blade
<head>
    @pixels
    <!-- Your other head content -->
</head>
```

Load specific platforms:

```blade
@pixels('facebook', 'tiktok')
```

Add noscript fallbacks:

```blade
<noscript>
    @pixelsNoscript
</noscript>
```

### Model Integration

Add the `TrackableEvents` trait to your User or Order model:

```php
use Ideacrafters\PixelManager\Traits\TrackableEvents;

class User extends Authenticatable
{
    use TrackableEvents;
}
```

Now track events directly on your models:

```php
$user->trackPurchase(99.99, 'USD');
$user->trackAddToCart($product->id, $product->price);
$user->trackViewContent($product->id, $product->name, $product->price);
```

## Artisan Commands

```bash
# Install and setup
php artisan pixel:install

# Add a new pixel
php artisan pixel:add

# Test a pixel configuration
php artisan pixel:test {id}
```

## Configuration

The configuration file includes:

- Platform adapter mappings
- Queue connection settings
- Deduplication settings
- Standard event definitions
- Cache configuration

See `config/pixels-manager.php` for all available options.

## Requirements

- PHP >= 8.0
- Laravel >= 9.0
- Queue driver: Redis (recommended) or Database
- Cache driver: Redis (recommended) or Memcached

## Testing

```bash
composer test
```

## Security

- AES-256 encryption for credentials stored in database
- SHA-256 hashing for PII (emails, phones) before sending
- HTTPS-only API communication
- Input validation and sanitization
- GDPR/CCPA consent management

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Amar Neche](https://github.com/)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
