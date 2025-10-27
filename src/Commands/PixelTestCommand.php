<?php

namespace Ideacrafters\PixelManager\Commands;

use Illuminate\Console\Command;
use Ideacrafters\PixelManager\Facades\PixelManager as PixelManagerFacade;

class PixelTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pixel:test {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a pixel configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pixelId = $this->argument('id');

        if (! $pixelId) {
            $pixel = $this->selectPixel();
        } else {
            $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
            $pixel = $modelClass::findOrFail($pixelId);
        }

        if (! $pixel) {
            $this->error('No pixel selected');

            return self::FAILURE;
        }

        $this->info("Testing pixel: {$pixel->platform} (ID: {$pixel->id})");
        $this->newLine();

        // Send a test event
        try {
            $data = [
                'value' => 0.00,
                'currency' => 'USD',
                'test_event' => true,
            ];

            $userData = [
                'email' => 'test@example.com',
            ];

            PixelManagerFacade::forPixels($pixel->id)
                ->track('PageView', $data, $userData);

            $this->info('✓ Test event dispatched successfully');
            $this->newLine();
            $this->info('Event Details:');
            $this->line("  Platform: {$pixel->platform}");
            $this->line("  Event: PageView");
            $this->line("  Pixel ID: {$pixel->pixel_id}");
            $this->newLine();
            $this->info('Check your pixel dashboard to verify the event was received.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send test event: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    protected function selectPixel()
    {
        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
        $pixels = $modelClass::active()->get();

        if ($pixels->isEmpty()) {
            $this->error('No active pixels found. Create one with: <comment>php artisan pixel:add</comment>');

            return null;
        }

        $choices = $pixels->map(function ($pixel) {
            return "{$pixel->platform} - {$pixel->pixel_id} (ID: {$pixel->id})";
        })->toArray();

        $selected = $this->choice('Select a pixel to test', $choices);

        $index = array_search($selected, $choices);

        return $pixels->get($index);
    }
}

