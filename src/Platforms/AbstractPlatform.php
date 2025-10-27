<?php

namespace Ideacrafters\PixelManager\Platforms;

use Ideacrafters\PixelManager\Contracts\PlatformPixelInterface;
use Ideacrafters\PixelManager\Exceptions\PlatformApiException;
use Ideacrafters\PixelManager\Models\Pixel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractPlatform implements PlatformPixelInterface
{
    protected Pixel $pixel;

    /**
     * Set the pixel configuration.
     */
    public function setPixel(Pixel $pixel): void
    {
        $this->pixel = $pixel;
    }

    /**
     * Get the API endpoint URL.
     */
    abstract protected function getApiEndpoint(): string;

    /**
     * Build the request headers.
     */
    abstract protected function getHeaders(): array;

    /**
     * Build the request payload.
     */
    abstract protected function buildPayload(string $event, array $data, array $userData): array;

    /**
     * Send the event to the platform API.
     */
    public function track(string $event, array $data = [], array $userData = []): bool
    {
        try {
            $payload = $this->buildPayload($event, $data, $userData);
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->getApiEndpoint(), $payload);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Pixel event failed', [
                'platform' => $this->pixel->platform,
                'event' => $event,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Pixel event exception', [
                'platform' => $this->pixel->platform,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            throw new PlatformApiException("Failed to track event: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Hash user data using SHA-256.
     */
    public function hashUserData(array $userData): array
    {
        $hashed = [];

        foreach ($userData as $key => $value) {
            if (in_array($key, ['email', 'phone_number', 'phone'])) {
                $hashed[$key] = hash('sha256', strtolower($value));
            } else {
                $hashed[$key] = $value;
            }
        }

        return $hashed;
    }

    /**
     * Validate credentials by sending a test request.
     */
    public function validateCredentials(): bool
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->getValidationEndpoint());

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation endpoint.
     */
    protected function getValidationEndpoint(): string
    {
        return $this->getApiEndpoint();
    }
}
