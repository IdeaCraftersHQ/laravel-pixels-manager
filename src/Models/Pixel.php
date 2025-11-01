<?php

namespace Ideacrafters\PixelManager\Models;

use Ideacrafters\PixelManager\Database\Factories\PixelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Pixel extends Model
{
    use HasFactory;

    protected static function newFactory(): PixelFactory
    {
        return PixelFactory::new();
    }

    protected $fillable = [
        'platform',
        'pixel_id',
        'access_token',
        'test_event_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the encrypted access token.
     */
    public function getAccessTokenAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Set the encrypted access token.
     */
    public function setAccessTokenAttribute(?string $value): void
    {
        if (is_null($value)) {
            $this->attributes['access_token'] = null;

            return;
        }

        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    /**
     * Scope to filter active pixels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by access token exists.
     */
    public function scopeHasAccessToken($query)
    {
        return $query->whereNotNull('access_token');
    }

    /**
     * Scope to filter by platform.
     */
    public function scopePlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Check if pixel has access token.
     */
    public function hasAccessToken(): bool
    {
        return ! empty($this->access_token);
    }
}
