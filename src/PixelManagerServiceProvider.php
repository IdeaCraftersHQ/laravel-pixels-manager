<?php

namespace Ideacrafters\PixelManager;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ideacrafters\PixelManager\Commands\PixelManagerCommand;
use Ideacrafters\PixelManager\Commands\PixelInstallCommand;
use Ideacrafters\PixelManager\Commands\PixelAddCommand;
use Ideacrafters\PixelManager\Commands\PixelTestCommand;

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
            ->hasMigration('create_pixels_manager_table')
            ->hasCommands([
                PixelManagerCommand::class,
                PixelInstallCommand::class,
                PixelAddCommand::class,
                PixelTestCommand::class,
            ]);
    }

    public function boot(): void
    {
        parent::boot();

        $this->app->singleton(\Ideacrafters\PixelManager\PixelManager::class);

        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives(): void
    {
        // @pixels directive
        Blade::directive('pixels', function ($platforms = null) {
            if ($platforms) {
                return "<?php echo view('pixels-manager::scripts.pixels', ['platforms' => explode(',', str_replace([' ', \"'\", '\"'], '', {$platforms}))])->render(); ?>";
            }

            return "<?php echo view('pixels-manager::scripts.pixels')->render(); ?>";
        });

        // @facebookPixel directive
        Blade::directive('facebookPixel', function () {
            return "<?php echo view('pixels-manager::scripts.facebook')->render(); ?>";
        });

        // @tiktokPixel directive
        Blade::directive('tiktokPixel', function () {
            return "<?php echo view('pixels-manager::scripts.tiktok')->render(); ?>";
        });

        // @snapchatPixel directive
        Blade::directive('snapchatPixel', function () {
            return "<?php echo view('pixels-manager::scripts.snapchat')->render(); ?>";
        });

        // @pixelsNoscript directive
        Blade::directive('pixelsNoscript', function () {
            return "<?php echo view('pixels-manager::scripts.noscript')->render(); ?>";
        });

        // @trackEvent directive
        Blade::directive('trackEvent', function ($data) {
            return "<?php echo '<script>window.pixelManager?.track(' . json_encode({$data}) . ', {}, {});</script>'; ?>";
        });

        // @pixelConsent directive
        Blade::directive('pixelConsent', function () {
            return "<?php echo view('pixels-manager::scripts.consent')->render(); ?>";
        });
    }
}
