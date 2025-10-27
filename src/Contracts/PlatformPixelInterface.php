<?php

namespace Ideacrafters\PixelManager\Contracts;

use Ideacrafters\PixelManager\Models\Pixel;

interface PlatformPixelInterface
{
    /**
     * Track an event on the platform.
     *
     * @param  string  $event
     * @param  array  $data
     * @param  array  $userData
     * @return bool
     */
    public function track(string $event, array $data = [], array $userData = []): bool;

    /**
     * Get the script tag for client-side tracking.
     *
     * @return string
     */
    public function getScriptTag(): string;

    /**
     * Get the noscript fallback tag.
     *
     * @return string
     */
    public function getNoscriptTag(): string;

    /**
     * Validate credentials against the platform API.
     *
     * @return bool
     */
    public function validateCredentials(): bool;

    /**
     * Map standard event name to platform-specific event name.
     *
     * @param  string  $standardEvent
     * @return string
     */
    public function mapEvent(string $standardEvent): string;

    /**
     * Set the pixel configuration.
     *
     * @param  \Ideacrafters\PixelManager\Models\Pixel  $pixel
     * @return void
     */
    public function setPixel(Pixel $pixel): void;

    /**
     * Hash user data (email, phone) according to platform requirements.
     *
     * @param  array  $userData
     * @return array
     */
    public function hashUserData(array $userData): array;
}

