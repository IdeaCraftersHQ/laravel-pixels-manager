<?php

use Ideacrafters\PixelManager\Exceptions\InvalidPlatformException;
use Ideacrafters\PixelManager\Models\Pixel;
use Ideacrafters\PixelManager\PixelManager;

beforeEach(function () {
    $this->pixelManager = new PixelManager;
    $this->pixel = Pixel::factory()->facebook()->create();
});

test('can validate platform', function () {
    expect($this->pixelManager->validatePlatform('facebook'))
        ->not->toThrow(InvalidPlatformException::class);
});

test('throws exception for invalid platform validation', function () {
    expect(fn () => $this->pixelManager->validatePlatform('invalid'))
        ->toThrow(InvalidPlatformException::class);
});

test('can add new pixel', function () {
    $pixel = $this->pixelManager->addPixel('facebook', '123456', 'test_token');

    expect($pixel)->toBeInstanceOf(Pixel::class)
        ->and($pixel->platform)->toBe('facebook')
        ->and($pixel->pixel_id)->toBe('123456');
});

test('can enable pixel', function () {
    $this->pixel->update(['is_active' => false]);

    $this->pixelManager->enablePixel($this->pixel->id);

    expect($this->pixel->fresh()->is_active)->toBeTrue();
});

test('can disable pixel', function () {
    $this->pixelManager->disablePixel($this->pixel->id);

    expect($this->pixel->fresh()->is_active)->toBeFalse();
});

test('can get active pixels', function () {
    $pixels = $this->pixelManager->getActivePixels();

    expect($pixels)->toHaveCount(1)
        ->and($pixels->first()->is_active)->toBeTrue();
});

test('forPixels accepts variadic ints', function () {
    $manager = new PixelManager;
    $manager->forPixels(1, 2, 3);

    $reflection = new ReflectionProperty(PixelManager::class, 'targetPixelIds');
    $reflection->setAccessible(true);

    expect($reflection->getValue($manager))->toBe([1, 2, 3]);
});

test('forPixels accepts an array of ints', function () {
    $manager = new PixelManager;
    $manager->forPixels([1, 2, 3]);

    $reflection = new ReflectionProperty(PixelManager::class, 'targetPixelIds');
    $reflection->setAccessible(true);

    expect($reflection->getValue($manager))->toBe([1, 2, 3]);
});

test('forPixels accepts a Collection', function () {
    $manager = new PixelManager;
    $manager->forPixels(collect([1, 2, 3]));

    $reflection = new ReflectionProperty(PixelManager::class, 'targetPixelIds');
    $reflection->setAccessible(true);

    expect($reflection->getValue($manager))->toBe([1, 2, 3]);
});

test('forPixels deduplicates pixel IDs', function () {
    $manager = new PixelManager;
    $manager->forPixels(1, 2, 2, 3, 3);

    $reflection = new ReflectionProperty(PixelManager::class, 'targetPixelIds');
    $reflection->setAccessible(true);

    expect($reflection->getValue($manager))->toBe([1, 2, 3]);
});

test('forPixels with empty collection sets empty array', function () {
    $manager = new PixelManager;
    $manager->forPixels(collect([]));

    $reflection = new ReflectionProperty(PixelManager::class, 'targetPixelIds');
    $reflection->setAccessible(true);

    expect($reflection->getValue($manager))->toBe([]);
});

test('track no-ops when forPixels receives empty input', function () {
    $manager = new PixelManager;
    $manager->forPixels(collect([]));

    // Should not throw and should return the manager instance
    $result = $manager->track('Purchase', ['value' => 100, 'currency' => 'USD']);

    expect($result)->toBeInstanceOf(PixelManager::class);
});
