@php
    $modelClass = config('pixels-manager.model', \Ideacrafters\PixelManager\Models\Pixel::class);
    $pixels = $modelClass::active()->get();

    if (isset($platforms) && is_array($platforms)) {
        $pixels = $pixels->whereIn('platform', $platforms);
    }
@endphp

@foreach ($pixels as $pixel)
    @if ($pixel->platform === 'facebook')
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixel->pixel_id }}&ev=PageView&noscript=1" />
    @elseif ($pixel->platform === 'snapchat')
        <img height="1" width="1" style="display:none" src="https://tr.snapchat.com/v2/pixel?data={}&amp;pixel_id={{ $pixel->pixel_id }}&amp;" />
    @endif
@endforeach

