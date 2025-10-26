<?php

namespace Ideacrafters\PixelManager;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ideacrafters\PixelManager\Commands\PixelManagerCommand;

class PixelManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pixels-manager')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_pixels_table')
            ->hasCommand(PixelManagerCommand::class);
    }
}
