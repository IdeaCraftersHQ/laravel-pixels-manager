<!-- TikTok Pixel Code -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t[e].q=t[e].q||[],t[e].q.push(arguments)}},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=[e,n],ttq._i[0]=ttq._i[0]||{},ttq._i[1]=ttq._i[1]||[],ttq._i[0].ttqid=e,ttq._i[0].ttqeventType=n;
var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
}(window, document, 'ttq');
ttq.load('{{ $pixelId }}');
ttq.page();
</script>
<!-- End TikTok Pixel Code -->

