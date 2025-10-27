<?php

use Ideacrafters\PixelManager\Models\Pixel;

test('access token is encrypted when saving', function () {
    $pixel = Pixel::factory()->create([
        'access_token' => 'sensitive_token_123',
    ]);

    $rawAttribute = $pixel->getAttributes()['access_token'];

    expect($rawAttribute)->not->toBe('sensitive_token_123');
});

test('access token is decrypted when retrieving', function () {
    $pixel = Pixel::factory()->create([
        'access_token' => 'sensitive_token_123',
    ]);

    expect($pixel->access_token)->toBe('sensitive_token_123');
});

test('can handle null access token', function () {
    $pixel = Pixel::factory()->create([
        'access_token' => null,
    ]);

    expect($pixel->access_token)->toBeNull();
});
