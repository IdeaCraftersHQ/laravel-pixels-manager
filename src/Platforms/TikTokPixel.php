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
                'page' => $this->buildPage(),
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

        if (isset($userData['external_id'])) {
            $mapped['external_id'] = $userData['external_id'];
        }

        if (isset($userData['ip'])) {
            $mapped['ip'] = $userData['ip'];
        }

        if (isset($userData['user_agent'])) {
            $mapped['user_agent'] = $userData['user_agent'];
        }

        if (isset($userData['ttp'])) {
            $mapped['ttp'] = $userData['ttp'];
        }

        // If not running from console, auto-populate missing fields from request/cookie
        if (!app()->runningInConsole()) {
            if (!isset($mapped['ip'])) {
                $mapped['ip'] = request()->ip();
            }

            if (!isset($mapped['user_agent'])) {
                $mapped['user_agent'] = request()->userAgent();
            }

            if (!isset($mapped['ttp'])) {
                $ttpCookie = request()->cookie('_ttp');
                if ($ttpCookie) {
                    $mapped['ttp'] = $ttpCookie;
                }
            }
        }

        return $mapped;
    }

    protected function buildPage(): array
    {
        $page = [];

        if (!app()->runningInConsole()) {
            $page['url'] = request()->fullUrl();
            $page['referrer'] = request()->header('referer');
        }

        return $page;
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
