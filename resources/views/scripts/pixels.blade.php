@php
    $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
    $pixels = $modelClass::active()->get();

    if (isset($platforms) && is_array($platforms)) {
        $pixels = $pixels->whereIn('platform', $platforms);
    }
@endphp

@foreach ($pixels as $pixel)
    @if ($pixel->platform === 'facebook')
        @include('pixels-manager::scripts.facebook', ['pixelId' => $pixel->pixel_id])
    @elseif ($pixel->platform === 'tiktok')
        @include('pixels-manager::scripts.tiktok', ['pixelId' => $pixel->pixel_id])
    @elseif ($pixel->platform === 'snapchat')
        @include('pixels-manager::scripts.snapchat', ['pixelId' => $pixel->pixel_id])
    @endif
@endforeach

