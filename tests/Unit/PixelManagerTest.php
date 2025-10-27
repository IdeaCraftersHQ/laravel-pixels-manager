<?php

use Ideacrafters\PixelManager\Exceptions\InvalidEventException;
use Ideacrafters\PixelManager\Exceptions\InvalidPlatformException;
use Ideacrafters\PixelManager\PixelManager;
use Ideacrafters\PixelManager\Models\Pixel;

beforeEach(function () {
    $this->pixelManager = new PixelManager();
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

