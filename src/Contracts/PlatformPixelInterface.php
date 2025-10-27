<?php

namespace Ideacrafters\PixelManager\Contracts;

use Ideacrafters\PixelManager\Models\Pixel;

interface PlatformPixelInterface
{
    /**
     * Track an event on the platform.
     */
    public function track(string $event, array $data = [], array $userData = []): bool;

    /**
     * Get the script tag for client-side tracking.
     */
    public function getScriptTag(): string;

    /**
     * Get the noscript fallback tag.
     */
    public function getNoscriptTag(): string;

    /**
     * Validate credentials against the platform API.
     */
    public function validateCredentials(): bool;

    /**
     * Map standard event name to platform-specific event name.
     */
    public function mapEvent(string $standardEvent): string;

    /**
     * Set the pixel configuration.
     */
    public function setPixel(Pixel $pixel): void;

    /**
     * Hash user data (email, phone) according to platform requirements.
     */
    public function hashUserData(array $userData): array;
}
