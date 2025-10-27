<?php

namespace Ideacrafters\PixelManager\Database\Factories;

use Ideacrafters\PixelManager\Models\Pixel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PixelFactory extends Factory
{
    protected $model = Pixel::class;

    public function definition(): array
    {
        return [
            'platform' => 'facebook',
            'pixel_id' => '123456789',
            'access_token' => 'test_access_token',
            'test_event_code' => null,
            'is_active' => true,
        ];
    }

    public function facebook(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'facebook',
            'pixel_id' => '123456789',
        ]);
    }

    public function tiktok(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'tiktok',
            'pixel_id' => 'C1234567890ABCDEF',
        ]);
    }

    public function snapchat(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'snapchat',
            'pixel_id' => 'abc123def456',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withTestEventCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'test_event_code' => $code,
        ]);
    }
}

