<?php

namespace Ideacrafters\PixelManager\Platforms;

class TikTokPixel extends AbstractPlatform
{
    protected function getApiEndpoint(): string
    {
        return 'https://business-api.tiktok.com/open_api/v1.3/event/track/';
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Access-Token' => $this->pixel->access_token,
        ];
    }

    protected function buildPayload(string $event, array $data = [], array $userData = []): array
    {
        $userData = $this->hashUserData($userData);

        return [
            'event' => [
                'event_id' => $this->generateEventId($event, $data, $userData),
                'timestamp' => now()->toISOString(),
                'pixel_code' => $this->pixel->pixel_id,
                'context' => [
                    'page' => [
                        'url' => request()->fullUrl(),
                        'referrer' => request()->header('referer'),
                    ],
                    'user_agent' => request()->userAgent(),
                    'ip' => request()->ip(),
                ],
                'properties' => [
                    'contents' => $data['contents'] ?? null,
                    'content_type' => $data['content_type'] ?? 'product',
                    'currency' => $data['currency'] ?? 'USD',
                    'value' => $data['value'] ?? 0,
                    'query' => $data['query'] ?? null,
                ],
                'user' => $this->buildUserData($userData),
            ],
            'partner_name' => 'Laravel Pixel Manager',
            'test_event_code' => $this->pixel->test_event_code,
        ];
    }

    protected function buildUserData(array $userData): array
    {
        $mapped = [];

        if (isset($userData['email'])) {
            $mapped['email'] = $userData['email'];
        }

        if (isset($userData['phone_number']) || isset($userData['phone'])) {
            $mapped['phone_number'] = $userData['phone_number'] ?? $userData['phone'];
        }

        if (isset($userData['first_name'])) {
            $mapped['first_name'] = $userData['first_name'];
        }

        if (isset($userData['last_name'])) {
            $mapped['last_name'] = $userData['last_name'];
        }

        if (isset($userData['external_id'])) {
            $mapped['external_id'] = $userData['external_id'];
        }

        if (isset($userData['city'])) {
            $mapped['city'] = $userData['city'];
        }

        if (isset($userData['state'])) {
            $mapped['state'] = $userData['state'];
        }

        if (isset($userData['zip'])) {
            $mapped['zip_code'] = $userData['zip'];
        }

        if (isset($userData['country'])) {
            $mapped['country_code'] = $userData['country'];
        }

        return $mapped;
    }

    public function getScriptTag(): string
    {
        return view('pixels-manager::scripts.tiktok', [
            'pixelId' => $this->pixel->pixel_id,
        ])->render();
    }

    public function getNoscriptTag(): string
    {
        return ''; // TikTok doesn't have a noscript tag
    }

    public function mapEvent(string $standardEvent): string
    {
        $mapping = [
            'PageView' => 'ViewContent',
            'ViewContent' => 'ViewContent',
            'Search' => 'Search',
            'AddToCart' => 'AddToCart',
            'InitiateCheckout' => 'InitiateCheckout',
            'Purchase' => 'PlaceAnOrder',
            'Lead' => 'SubmitForm',
            'CompleteRegistration' => 'CompleteRegistration',
        ];

        return $mapping[$standardEvent] ?? 'CompletePayment';
    }

    protected function generateEventId(string $event, array $data, array $userData): string
    {
        $parts = [
            $event,
            $this->pixel->pixel_id,
            now()->toDateTimeString(),
            json_encode($data),
        ];

        return hash('sha256', implode('|', $parts));
    }
}
