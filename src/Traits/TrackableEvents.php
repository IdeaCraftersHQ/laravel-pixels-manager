<?php

namespace Ideacrafters\PixelManager\Traits;

use Ideacrafters\PixelManager\Facades\PixelManager;

trait TrackableEvents
{
    /**
     * Track a custom event.
     *
     * @param  string|null  $platforms  Comma-separated list of platforms or null for all
     */
    public function trackEvent(string $event, array $data = [], ?string $platforms = null): void
    {
        $userData = $this->getTrackingUserData();

        if ($platforms) {
            PixelManager::forPlatforms(...explode(',', $platforms))
                ->track($event, $data, $userData);
        } else {
            PixelManager::track($event, $data, $userData);
        }
    }

    /**
     * Track a page view event.
     */
    public function trackPageView(?string $url = null, ?string $platforms = null): void
    {
        $data = [];

        if ($url) {
            $data['url'] = $url;
        }

        $this->trackEvent('PageView', $data, $platforms);
    }

    /**
     * Track a view content event.
     *
     * @param  string|int  $contentId
     */
    public function trackViewContent($contentId, string $contentName, ?float $value = null, ?string $platforms = null): void
    {
        $data = [
            'content_id' => $contentId,
            'content_name' => $contentName,
            'content_type' => 'product',
        ];

        if ($value !== null) {
            $data['value'] = $value;
        }

        $this->trackEvent('ViewContent', $data, $platforms);
    }

    /**
     * Track an add to cart event.
     *
     * @param  string|int  $contentId
     */
    public function trackAddToCart($contentId, float $value, string $currency = 'USD', ?string $platforms = null): void
    {
        $data = [
            'content_id' => $contentId,
            'value' => $value,
            'currency' => $currency,
            'content_type' => 'product',
        ];

        $this->trackEvent('AddToCart', $data, $platforms);
    }

    /**
     * Track an initiate checkout event.
     */
    public function trackInitiateCheckout(float $value, string $currency = 'USD', array $contents = [], ?string $platforms = null): void
    {
        $data = [
            'value' => $value,
            'currency' => $currency,
            'contents' => $contents,
            'num_items' => count($contents),
        ];

        $this->trackEvent('InitiateCheckout', $data, $platforms);
    }

    /**
     * Track a purchase event.
     */
    public function trackPurchase(float $value, string $currency = 'USD', array $contents = [], ?string $platforms = null): void
    {
        $data = [
            'value' => $value,
            'currency' => $currency,
            'contents' => $contents,
            'num_items' => count($contents),
        ];

        $this->trackEvent('Purchase', $data, $platforms);
    }

    /**
     * Get user data for tracking.
     * Override this method to provide custom user data.
     */
    protected function getTrackingUserData(): array
    {
        $userData = [];

        // Try to get user identifier
        if (method_exists($this, 'getAuthIdentifier')) {
            $userData['external_id'] = (string) $this->getAuthIdentifier();
        }

        // Try to get user email
        if (isset($this->email)) {
            $userData['email'] = $this->email;
        } elseif (method_exists($this, 'getEmailForVerification')) {
            $userData['email'] = $this->getEmailForVerification();
        }

        // Try to get user phone
        if (isset($this->phone)) {
            $userData['phone_number'] = $this->phone;
        }

        // Try to get user name
        if (isset($this->first_name)) {
            $userData['first_name'] = $this->first_name;
        }

        if (isset($this->last_name)) {
            $userData['last_name'] = $this->last_name;
        }

        return $userData;
    }
}
