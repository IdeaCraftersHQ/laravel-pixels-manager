<?php

namespace Ideacrafters\PixelManager\Platforms;

class FacebookPixel extends AbstractPlatform
{
    protected function getApiEndpoint(): string
    {
        return "https://graph.facebook.com/v18.0/{$this->pixel->pixel_id}/events";
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(string $event, array $data = [], array $userData = []): array
    {
        $userData = $this->hashUserData($userData);

        return [
            'data' => [
                [
                    'event_name' => $this->mapEvent($event),
                    'event_time' => now()->timestamp,
                    'event_id' => $this->generateEventId($event, $data, $userData),
                    'event_source_url' => request()->fullUrl(),
                    'action_source' => 'website',
                    'user_data' => $this->buildUserData($userData),
                    'custom_data' => $this->buildCustomData($data),
                    'test_event_code' => $this->pixel->test_event_code,
                ],
            ],
            'access_token' => $this->pixel->access_token,
        ];
    }

    protected function buildUserData(array $userData): array
    {
        $mapped = [];

        if (isset($userData['email'])) {
            $mapped['em'] = $userData['email'];
        }

        if (isset($userData['phone_number']) || isset($userData['phone'])) {
            $mapped['ph'] = $userData['phone_number'] ?? $userData['phone'];
        }

        if (isset($userData['first_name'])) {
            $mapped['fn'] = $userData['first_name'];
        }

        if (isset($userData['last_name'])) {
            $mapped['ln'] = $userData['last_name'];
        }

        if (isset($userData['city'])) {
            $mapped['ct'] = $userData['city'];
        }

        if (isset($userData['state'])) {
            $mapped['st'] = $userData['state'];
        }

        if (isset($userData['zip'])) {
            $mapped['zp'] = $userData['zip'];
        }

        if (isset($userData['country'])) {
            $mapped['country'] = $userData['country'];
        }

        return $mapped;
    }

    protected function buildCustomData(array $data): array
    {
        $mapped = [];

        if (isset($data['value'])) {
            $mapped['value'] = $data['value'];
        }

        if (isset($data['currency'])) {
            $mapped['currency'] = $data['currency'];
        }

        if (isset($data['content_ids'])) {
            $mapped['content_ids'] = is_array($data['content_ids']) ? $data['content_ids'] : [$data['content_ids']];
        }

        if (isset($data['content_name'])) {
            $mapped['content_name'] = $data['content_name'];
        }

        if (isset($data['content_category'])) {
            $mapped['content_category'] = $data['content_category'];
        }

        if (isset($data['content_type'])) {
            $mapped['content_type'] = $data['content_type'];
        }

        if (isset($data['num_items'])) {
            $mapped['num_items'] = $data['num_items'];
        }

        if (isset($data['contents'])) {
            $mapped['contents'] = $data['contents'];
        }

        return $mapped;
    }

    public function getScriptTag(): string
    {
        return view('pixels-manager::scripts.facebook', [
            'pixelId' => $this->pixel->pixel_id,
        ])->render();
    }

    public function getNoscriptTag(): string
    {
        return view('pixels-manager::scripts.noscript-facebook', [
            'pixelId' => $this->pixel->pixel_id,
        ])->render();
    }

    public function mapEvent(string $standardEvent): string
    {
        $mapping = [
            'PageView' => 'PageView',
            'ViewContent' => 'ViewContent',
            'Search' => 'Search',
            'AddToCart' => 'AddToCart',
            'AddToWishlist' => 'AddToWishlist',
            'InitiateCheckout' => 'InitiateCheckout',
            'AddPaymentInfo' => 'AddPaymentInfo',
            'Purchase' => 'Purchase',
            'Lead' => 'Lead',
            'CompleteRegistration' => 'CompleteRegistration',
            'Contact' => 'Contact',
            'CustomizeProduct' => 'CustomizeProduct',
            'Donate' => 'Donate',
            'FindLocation' => 'FindLocation',
            'Schedule' => 'Schedule',
            'StartTrial' => 'StartTrial',
            'SubmitApplication' => 'SubmitApplication',
            'Subscribe' => 'Subscribe',
        ];

        return $mapping[$standardEvent] ?? $standardEvent;
    }

    protected function generateEventId(string $event, array $data, array $userData): string
    {
        $parts = [
            $event,
            $this->pixel->pixel_id,
            now()->toDateTimeString(),
            json_encode($data),
            json_encode($userData),
            request()->ip(),
        ];

        return hash('sha256', implode('|', $parts));
    }
}
