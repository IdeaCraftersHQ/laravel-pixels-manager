<?php

namespace Ideacrafters\PixelManager\Commands;

use Ideacrafters\PixelManager\Exceptions\InvalidPlatformException;
use Ideacrafters\PixelManager\Facades\PixelManager;
use Ideacrafters\PixelManager\Models\Pixel;
use Illuminate\Console\Command;

class PixelAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pixel:add {--platform=} {--pixel-id=} {--access-token=} {--test-event-code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new pixel configuration interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Add a New Pixel Configuration');
        $this->newLine();

        // Get platform
        $platform = $this->option('platform') ?? $this->choice(
            'Select platform',
            ['facebook', 'tiktok', 'snapchat'],
            0
        );

        // Validate platform
        try {
            PixelManager::validatePlatform($platform);
        } catch (InvalidPlatformException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        // Get pixel ID
        $pixelId = $this->option('pixel-id') ?? $this->ask('Enter Pixel ID');

        if (empty($pixelId)) {
            $this->error('Pixel ID is required');

            return self::FAILURE;
        }

        // Get access token
        $accessToken = $this->option('access-token') ?? $this->secret('Enter Access Token (leave blank for client-side only)');

        // Get test event code (optional)
        $testEventCode = $this->option('test-event-code') ?? $this->ask('Enter Test Event Code (optional)', null);

        // Create pixel
        try {
            $pixel = PixelManager::addPixel($platform, $pixelId, $accessToken);

            if ($testEventCode) {
                $pixel->update(['test_event_code' => $testEventCode]);
            }

            $this->newLine();
            $this->info("✓ Pixel configuration saved (ID: {$pixel->id})");
            $this->newLine();
            $this->info('Next steps:');
            $this->line('  1. Test the configuration: <comment>php artisan pixel:test '.$pixel->id.'</comment>');
            $this->line('  2. Add the pixel to your Blade layout: <comment>@pixels</comment>');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to save pixel: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
