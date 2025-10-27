<?php

namespace Ideacrafters\PixelManager\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ideacrafters\PixelManager\Contracts\PlatformPixelInterface;
use Ideacrafters\PixelManager\Exceptions\PlatformApiException;

class SendPixelEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Model $pixel,
        public string $event,
        public array $data = [],
        public array $userData = []
    ) {
        $this->onConnection(config('pixels-manager.queue.connection'));
        $this->onQueue(config('pixels-manager.queue.queue'));
    }

    public function handle(): void
    {
        try {
            $platformAdapter = $this->getPlatformAdapter();
            $platformAdapter->setPixel($this->pixel);

            $success = $platformAdapter->track($this->event, $this->data, $this->userData);

            if (! $success) {
                throw new PlatformApiException("Failed to send event to platform: {$this->pixel->platform}");
            }
        } catch (\Exception $e) {
            Log::error('Pixel event job failed', [
                'pixel_id' => $this->pixel->id,
                'platform' => $this->pixel->platform,
                'event' => $this->event,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function attempts(): int
    {
        return config('pixels-manager.queue.tries', 3);
    }

    public function backoff(): array
    {
        return config('pixels-manager.queue.backoff', [60, 300, 900]);
    }

    protected function getPlatformAdapter(): PlatformPixelInterface
    {
        $platforms = config('pixels-manager.platforms', []);
        $platformClass = $platforms[$this->pixel->platform] ?? null;

        if (! $platformClass || ! class_exists($platformClass)) {
            throw new \RuntimeException("Platform adapter not found for: {$this->pixel->platform}");
        }

        $adapter = app($platformClass);

        if (! $adapter instanceof PlatformPixelInterface) {
            throw new \RuntimeException("Platform adapter must implement PlatformPixelInterface");
        }

        return $adapter;
    }
}

