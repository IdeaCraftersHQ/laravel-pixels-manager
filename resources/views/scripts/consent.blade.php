<!-- Pixel Consent Management -->
<script>
window.pixelManager = window.pixelManager || {};
window.pixelManager.consent = {{ session('pixel_consent', false) ? 'true' : 'false' }};

window.pixelManager.setConsent = function(hasConsent) {
    window.pixelManager.consent = hasConsent;
    @if(config('session.driver') !== 'array')
        fetch('{{ route("pixels.consent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({consent: hasConsent})
        });
    @endif
};

window.pixelManager.setUserData = function(userData) {
    window.pixelManager.userData = userData;
};

window.pixelManager.track = function(event, data, userData) {
    if (!window.pixelManager.consent) return;

    userData = userData || window.pixelManager.userData || {};

    @if(request()->user())
        userData.external_id = '{{ request()->user()->id }}';
        @if(request()->user()->email)
            userData.email = '{{ request()->user()->email }}';
        @endif
    @endif

    data = data || {};
    data.userData = userData;

    // Client-side tracking for immediate feedback
    @foreach(['facebook', 'tiktok', 'snapchat'] as $platform)
        @if(request()->user())
            // Track to {{ $platform }}
        @endif
    @endforeach
};
</script>
<!-- End Pixel Consent Management -->

