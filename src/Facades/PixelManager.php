<?php

namespace Ideacrafters\PixelManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ideacrafters\PixelManager\PixelManager
 */
class PixelManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ideacrafters\PixelManager\PixelManager::class;
    }
}
