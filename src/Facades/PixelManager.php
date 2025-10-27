<?php

namespace Ideacrafters\PixelManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static track(string $event, array $data = [], array $userData = [])
 * @method static static forPlatforms(string ...$platforms)
 * @method static static forPixels(int ...$pixelIds)
 * @method static static except(string ...$platforms)
 * @method static Pixel addPixel(string $platform, string $pixelId, ?string $accessToken = null)
 * @method static bool enablePixel(int $id)
 * @method static bool disablePixel(int $id)
 * @method static \Illuminate\Database\Eloquent\Collection getActivePixels()
 * @method static void validatePlatform(string $platform)
 *
 * @see \Ideacrafters\PixelManager\PixelManager
 */
class PixelManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ideacrafters\PixelManager\PixelManager::class;
    }
}
