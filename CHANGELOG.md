# Changelog

All notable changes to `laravel-pixels-manager` will be documented in this file.

## Release 1.2.0 Queue-safe CAPI, custom event_id, extraUserData — 2026-03-31

### What's Changed

**`TrackableEvents` trait:**
- `trackEvent()` now accepts an optional `array $extraUserData` parameter that merges with `getTrackingUserData()`. Pass browser context (`client_ip_address`, `client_user_agent`, `fbp`, `fbc`) when firing events from HTTP context for use in queued CAPI calls.

**`FacebookPixel`:**
- `buildPayload()` accepts `event_id` in `$data` for client-server dedup — falls back to auto-generated ID
- `buildPayload()` accepts `event_source_url` in `$data` — falls back to `config('app.url')` in queue context instead of calling `request()->fullUrl()`
- `buildPayload()` strips `event_id` and `event_source_url` from `custom_data` before sending
- `buildUserData()` now maps `external_id` (was extracted by `getTrackingUserData()` but silently dropped)
- `generateEventId()` uses `$userData['client_ip_address']` in queue context instead of `request()->ip()` which returns `127.0.0.1` from Horizon

### Upgrade Guide

No breaking changes. The new `$extraUserData` parameter is optional with a default empty array.

**Full Changelog**: https://github.com/IdeaCraftersHQ/laravel-pixels-manager/compare/1.1.0...1.2.0

## Release 1.1.0 Fix forPixels() to accept flexible inputs - 2026-03-10

### What's Changed

* Bump dependabot/fetch-metadata from 2.4.0 to 2.5.0 by @dependabot[bot] in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/4
* Fix forPixels() to accept array and Collection inputs by @y-boudehane in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/5

### New Contributors

* @y-boudehane made their first contribution in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/5

**Full Changelog**: https://github.com/IdeaCraftersHQ/laravel-pixels-manager/compare/1.0.2...1.1.0

## Release 1.0.2 Fixed fbc & fbq - 2025-11-27

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/1
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/2
* Additions by @BitI3yBit in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/3

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/1
* @BitI3yBit made their first contribution in https://github.com/IdeaCraftersHQ/laravel-pixels-manager/pull/3

**Full Changelog**: https://github.com/IdeaCraftersHQ/laravel-pixels-manager/compare/1.0.1...1.0.2

## 1.0.0 - 2024-XX-XX

### Added

- Initial release of Laravel Pixel Manager
- Multi-platform support for Facebook, TikTok, and Snapchat
- Queue-based async event processing
- Selective platform and pixel targeting
- Blade directives for easy integration
- TrackableEvents trait for model integration
- AES-256 encryption for secure credential storage
- SHA-256 hashing for PII data
- Cache-based event deduplication
- Artisan commands: install, add, and test
- Comprehensive test suite
