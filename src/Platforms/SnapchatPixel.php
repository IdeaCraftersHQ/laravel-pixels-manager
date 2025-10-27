<?php

namespace Ideacrafters\PixelManager\Platforms;

class SnapchatPixel extends AbstractPlatform
{
    protected function getApiEndpoint(): string
    {
        return 'https://tr.snapchat.com/v2/conversion';
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->pixel->access_token,
        ];
    }

    protected function buildPayload(string $event, array $data = [], array $userData = []): array
    {
        $userData = $this->hashUserData($userData);

        return [
            'pixel_id' => $this->pixel->pixel_id,
            'event' => $this->mapEvent($event),
            'timestamp' => now()->timestamp,
            'event_conversion_id' => $this->generateEventId($event, $data, $userData),
            'hashed_email' => $userData['email'] ?? null,
            'hashed_phone_number' => $userData['phone_number'] ?? $userData['phone'] ?? null,
            'hashed_ip_address' => $userData['ip_address'] ?? hash('sha256', request()->ip()),
            'user_agent' => request()->userAgent(),
            'price' => $data['value'] ?? 0,
            'currency' => $data['currency'] ?? 'USD',
            'trans_id' => $data['trans_id'] ?? uniqid(),
            'item_ids' => $data['content_ids'] ?? [],
            'item_category' => $data['content_category'] ?? null,
            'signature' => null, // Will be calculated if provided
        ];
    }

    public function getScriptTag(): string
    {
        return view('pixels-manager::scripts.snapchat', [
            'pixelId' => $this->pixel->pixel_id,
        ])->render();
    }

    public function getNoscriptTag(): string
    {
        return view('pixels-manager::scripts.noscript-snapchat', [
            'pixelId' => $this->pixel->pixel_id,
        ])->render();
    }

    public function mapEvent(string $standardEvent): string
    {
        $mapping = [
            'PageView' => 'PAGE_VIEW',
            'ViewContent' => 'VIEW_CONTENT',
            'Search' => 'SEARCH',
            'AddToCart' => 'ADD_CART',
            'InitiateCheckout' => 'START_CHECKOUT',
            'Purchase' => 'PURCHASE',
            'Lead' => 'SIGN_UP',
            'CompleteRegistration' => 'SIGN_UP',
        ];

        return $mapping[$standardEvent] ?? 'CUSTOM';
    }

    protected function generateEventId(string $event, array $data, array $userData): string
    {
        $parts = [
            $event,
            $this->pixel->pixel_id,
            now()->toDateTimeString(),
            request()->ip(),
        ];

        return hash('sha256', implode('|', $parts));
    }
}
