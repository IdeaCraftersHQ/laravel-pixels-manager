<?php

namespace Ideacrafters\PixelManager;

use Illuminate\Support\Facades\Cache;
use Ideacrafters\PixelManager\Exceptions\InvalidEventException;
use Ideacrafters\PixelManager\Exceptions\InvalidPlatformException;
use Ideacrafters\PixelManager\Exceptions\PixelNotFoundException;
use Ideacrafters\PixelManager\Jobs\SendPixelEvent;
use Ideacrafters\PixelManager\Models\Pixel;

class PixelManager
{
    protected ?array $targetPlatforms = null;
    protected ?array $targetPixelIds = null;
    protected bool $excludeMode = false;

    /**
     * Track an event to all active pixels or selectively targeted ones.
     *
     * @param  string  $event
     * @param  array  $data
     * @param  array  $userData
     * @return static
     */
    public function track(string $event, array $data = [], array $userData = []): static
    {
        $this->validateEvent($event);

        $pixels = $this->getApplicablePixels();

        foreach ($pixels as $pixel) {
            // Check deduplication
            if ($this->shouldDeduplicate() && $this->isDuplicate($pixel, $event, $data, $userData)) {
                continue;
            }

            // Dispatch job to queue
            SendPixelEvent::dispatch($pixel, $event, $data, $userData);

            // Store deduplication hash
            if ($this->shouldDeduplicate()) {
                $this->storeDeduplicationHash($pixel, $event, $data, $userData);
            }
        }

        // Reset targeting
        $this->resetTargeting();

        return $this;
    }

    /**
     * Target specific platforms.
     *
     * @param  string  ...$platforms
     * @return static
     */
    public function forPlatforms(string ...$platforms): static
    {
        $this->validatePlatforms($platforms);
        $this->targetPlatforms = $platforms;
        $this->targetPixelIds = null;

        return $this;
    }

    /**
     * Target specific pixel IDs.
     *
     * @param  int  ...$pixelIds
     * @return static
     */
    public function forPixels(int ...$pixelIds): static
    {
        $this->targetPixelIds = $pixelIds;
        $this->targetPlatforms = null;

        return $this;
    }

    /**
     * Exclude specific platforms.
     *
     * @param  string  ...$platforms
     * @return static
     */
    public function except(string ...$platforms): static
    {
        $this->validatePlatforms($platforms);
        $allPlatforms = array_keys(config('pixels-manager.platforms', []));
        $this->targetPlatforms = array_diff($allPlatforms, $platforms);
        $this->excludeMode = true;

        return $this;
    }

    /**
     * Add a new pixel configuration.
     *
     * @param  string  $platform
     * @param  string  $pixelId
     * @param  string|null  $accessToken
     * @return \Ideacrafters\PixelManager\Models\Pixel
     */
    public function addPixel(string $platform, string $pixelId, ?string $accessToken = null)
    {
        $this->validatePlatform($platform);

        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);

        return $modelClass::create([
            'platform' => $platform,
            'pixel_id' => $pixelId,
            'access_token' => $accessToken,
            'is_active' => true,
        ]);
    }

    /**
     * Enable a pixel.
     *
     * @param  int  $id
     * @return bool
     */
    public function enablePixel(int $id): bool
    {
        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
        $pixel = $modelClass::findOrFail($id);
        $pixel->update(['is_active' => true]);

        return true;
    }

    /**
     * Disable a pixel.
     *
     * @param  int  $id
     * @return bool
     */
    public function disablePixel(int $id): bool
    {
        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
        $pixel = $modelClass::findOrFail($id);
        $pixel->update(['is_active' => false]);

        return true;
    }

    /**
     * Get all active pixels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivePixels()
    {
        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);

        return $modelClass::active()->get();
    }

    /**
     * Get pixels filtered by targeting criteria.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getApplicablePixels()
    {
        $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
        $query = $modelClass::active();

        if ($this->targetPixelIds !== null) {
            $query->whereIn('id', $this->targetPixelIds);
        }

        if ($this->targetPlatforms !== null) {
            $query->whereIn('platform', $this->targetPlatforms);
        }

        return $query->get();
    }

    /**
     * Validate event name.
     *
     * @param  string  $event
     * @return void
     * @throws \Ideacrafters\PixelManager\Exceptions\InvalidEventException
     */
    protected function validateEvent(string $event): void
    {
        $standardEvents = config('pixels-manager.standard_events', []);

        if (! in_array($event, $standardEvents)) {
            throw new InvalidEventException("Event '{$event}' is not a valid standard event.");
        }
    }

    /**
     * Validate platform name.
     *
     * @param  string  $platform
     * @return void
     * @throws \Ideacrafters\PixelManager\Exceptions\InvalidPlatformException
     */
    public function validatePlatform(string $platform): void
    {
        $platforms = array_keys(config('pixels-manager.platforms', []));

        if (! in_array($platform, $platforms)) {
            throw new InvalidPlatformException("Platform '{$platform}' is not supported.");
        }
    }

    /**
     * Validate array of platform names.
     *
     * @param  array  $platforms
     * @return void
     * @throws \Ideacrafters\PixelManager\Exceptions\InvalidPlatformException
     */
    protected function validatePlatforms(array $platforms): void
    {
        foreach ($platforms as $platform) {
            $this->validatePlatform($platform);
        }
    }

    /**
     * Check if deduplication is enabled.
     *
     * @return bool
     */
    protected function shouldDeduplicate(): bool
    {
        return config('pixels-manager.advanced.deduplication', true);
    }

    /**
     * Check if this event is a duplicate.
     *
     * @param  \Ideacrafters\PixelManager\Models\Pixel  $pixel
     * @param  string  $event
     * @param  array  $data
     * @param  array  $userData
     * @return bool
     */
    protected function isDuplicate(Pixel $pixel, string $event, array $data, array $userData): bool
    {
        $hash = $this->generateDeduplicationHash($pixel, $event, $data, $userData);
        $cacheKey = $this->getDeduplicationCacheKey($hash);

        return Cache::has($cacheKey);
    }

    /**
     * Store deduplication hash in cache.
     *
     * @param  \Ideacrafters\PixelManager\Models\Pixel  $pixel
     * @param  string  $event
     * @param  array  $data
     * @param  array  $userData
     * @return void
     */
    protected function storeDeduplicationHash(Pixel $pixel, string $event, array $data, array $userData): void
    {
        $hash = $this->generateDeduplicationHash($pixel, $event, $data, $userData);
        $cacheKey = $this->getDeduplicationCacheKey($hash);
        $window = config('pixels-manager.advanced.deduplication_window', 3600);

        Cache::put($cacheKey, true, $window);
    }

    /**
     * Generate deduplication hash.
     *
     * @param  \Ideacrafters\PixelManager\Models\Pixel  $pixel
     * @param  string  $event
     * @param  array  $data
     * @param  array  $userData
     * @return string
     */
    protected function generateDeduplicationHash(Pixel $pixel, string $event, array $data, array $userData): string
    {
        $parts = [
            $pixel->id,
            $event,
            json_encode($data),
            json_encode($userData),
            request()->ip(),
        ];

        return hash('sha256', implode('|', $parts));
    }

    /**
     * Get deduplication cache key.
     *
     * @param  string  $hash
     * @return string
     */
    protected function getDeduplicationCacheKey(string $hash): string
    {
        $prefix = config('pixels-manager.cache.prefix', 'pixel_manager');

        return "{$prefix}:dedup:{$hash}";
    }

    /**
     * Reset targeting options.
     *
     * @return void
     */
    protected function resetTargeting(): void
    {
        $this->targetPlatforms = null;
        $this->targetPixelIds = null;
        $this->excludeMode = false;
    }
}
