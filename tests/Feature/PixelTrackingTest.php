<?php

use Ideacrafters\PixelManager\Facades\PixelManager;
use Ideacrafters\PixelManager\Jobs\SendPixelEvent;
use Ideacrafters\PixelManager\Models\Pixel;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->pixel = Pixel::factory()->facebook()->create();
});

test('can track event to all active pixels', function () {
    Queue::fake();

    PixelManager::track('PageView', ['url' => 'https://example.com']);

    Queue::assertPushed(SendPixelEvent::class, function ($job) {
        return $job->event === 'PageView';
    });
});

test('can track event to specific platforms', function () {
    Queue::fake();

    PixelManager::forPlatforms('facebook')->track('Purchase', ['value' => 100]);

    Queue::assertPushed(SendPixelEvent::class, function ($job) {
        return $job->event === 'Purchase' && $job->data['value'] === 100;
    });
});

test('can exclude platforms from tracking', function () {
    Queue::fake();

    $this->pixel = Pixel::factory()->tiktok()->create();

    PixelManager::except('tiktok')->track('PageView');

    Queue::assertNotPushed(SendPixelEvent::class);
});

test('throws exception for invalid event', function () {
    expect(fn () => PixelManager::track('InvalidEvent'))
        ->toThrow(\Ideacrafters\PixelManager\Exceptions\InvalidEventException::class);
});

test('throws exception for invalid platform', function () {
    expect(fn () => PixelManager::forPlatforms('invalid-platform'))
        ->toThrow(\Ideacrafters\PixelManager\Exceptions\InvalidPlatformException::class);
});

