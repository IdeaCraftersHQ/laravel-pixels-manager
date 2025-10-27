<?php

namespace Ideacrafters\PixelManager\Testing;

use Ideacrafters\PixelManager\PixelManager;

class PixelManagerFake
{
    protected array $trackedEvents = [];

    public function track(string $event, array $data = [], array $userData = []): void
    {
        $this->trackedEvents[] = [
            'event' => $event,
            'data' => $data,
            'userData' => $userData,
            'timestamp' => now(),
        ];
    }

    public function assertTracked(string $event): void
    {
        $found = collect($this->trackedEvents)->contains('event', $event);

        if (! $found) {
            throw new \PHPUnit\Framework\AssertionFailedError(
                "Expected event '{$event}' was not tracked."
            );
        }
    }

    public function assertTrackedWith(string $event, array $data): void
    {
        $found = collect($this->trackedEvents)->first(function ($tracked) use ($event, $data) {
            return $tracked['event'] === $event && $tracked['data'] === $data;
        });

        if (! $found) {
            throw new \PHPUnit\Framework\AssertionFailedError(
                "Expected event '{$event}' with data was not tracked."
            );
        }
    }

    public function getTrackedEvents(): array
    {
        return $this->trackedEvents;
    }

    public function reset(): void
    {
        $this->trackedEvents = [];
    }
}

