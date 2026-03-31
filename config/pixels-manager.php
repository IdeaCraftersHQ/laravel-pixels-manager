<?php

use Ideacrafters\PixelManager\Models\Pixel;
use Ideacrafters\PixelManager\Platforms\FacebookPixel;
use Ideacrafters\PixelManager\Platforms\SnapchatPixel;
use Ideacrafters\PixelManager\Platforms\TikTokPixel;

// config for Ideacrafters/PixelManager
return [
    /*
    |--------------------------------------------------------------------------
    | Pixel Model
    |--------------------------------------------------------------------------
    |
    | The model class used for pixel storage. You can extend the base Pixel
    | model and register your custom model here to add relationships,
    | custom methods, or override existing behavior.
    |
    */
    'model' => Pixel::class,

    /*
    |--------------------------------------------------------------------------
    | Platform Adapters
    |--------------------------------------------------------------------------
    |
    | Define the platform adapter classes for each supported platform.
    | You can add custom platforms by creating your own adapter.
    |
    */
    'platforms' => [
        'facebook' => FacebookPixel::class,
        'tiktok' => TikTokPixel::class,
        'snapchat' => SnapchatPixel::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how pixel events are queued and processed.
    | Recommended: Use Redis for better performance.
    |
    */
    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => env('QUEUE_PIXEL_NAME', 'default'),
        'tries' => 3,
        'backoff' => [60, 300, 900], // 1min, 5min, 15min
    ],

    /*
    |--------------------------------------------------------------------------
    | Deduplication Settings
    |--------------------------------------------------------------------------
    |
    | Prevent duplicate events using cache-based deduplication.
    | Events are hashed and stored in cache for the specified duration.
    |
    */
    'advanced' => [
        'deduplication' => true,
        'deduplication_window' => 3600, // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Standard Events
    |--------------------------------------------------------------------------
    |
    | Define standard e-commerce events supported by pixel platforms.
    | These events are mapped to platform-specific event names.
    |
    */
    'standard_events' => [
        'PageView',
        'ViewContent',
        'Search',
        'AddToCart',
        'AddToWishlist',
        'InitiateCheckout',
        'AddPaymentInfo',
        'Purchase',
        'Lead',
        'CompleteRegistration',
        'Contact',
        'CustomizeProduct',
        'Donate',
        'FindLocation',
        'Schedule',
        'StartTrial',
        'SubmitApplication',
        'Subscribe',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching pixel data and deduplication hashes.
    |
    */
    'cache' => [
        'prefix' => 'pixel_manager',
        'driver' => env('CACHE_DRIVER', 'file'),
    ],
];
