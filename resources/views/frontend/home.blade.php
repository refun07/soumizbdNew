<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="storefront-release" content="20261028-startup-2">
    <script>
        // Start before stylesheets or third-party resources can delay the parser.
        (function () {
            document.documentElement.classList.add('storefront-js');
            var diagnostic = /(?:^|[?&])loading-check=3(?:&|$)/.test(window.location.search);
            var signals = {};
            function signal(stage, file, line) {
                if (!diagnostic || signals[stage]) return;
                signals[stage] = true;
                var image = new Image();
                image.src = '/images/storefront/soumis-logo.png?startup-check=2&stage=' + stage +
                    '&asset=' + encodeURIComponent(file || '') + '&line=' + (line || 0) + '&time=' + Date.now();
            }
            function recover() {
                var boot = document.querySelector('.storefront-boot');
                if (boot) {
                    boot.classList.add('storefront-boot-failed');
                    boot.setAttribute('aria-label', 'Store loading delayed');
                    signal('timeout');
                } else if (document.readyState === 'loading') {
                    window.setTimeout(recover, 250);
                }
            }
            signal('head');
            window.setTimeout(recover, 12000);
            window.addEventListener('error', function (event) {
                var target = event.target;
                if (target && (target.tagName === 'SCRIPT' || target.tagName === 'LINK')) {
                    var source = target.src || target.href || '';
                    if (source.indexOf('/build/') !== -1) signal('asset-error', source.split('/').pop().split('?')[0]);
                } else if (document.querySelector('.storefront-boot')) {
                    var name = event.error && event.error.name || 'Error';
                    signal('script-' + name.replace(/[^a-zA-Z]/g, '').slice(0, 24),
                        (event.filename || '').split('/').pop().split('?')[0], event.lineno);
                }
            }, true);
            window.addEventListener('unhandledrejection', function () {
                if (document.querySelector('.storefront-boot')) signal('rejected');
            });
            document.addEventListener('DOMContentLoaded', function () { signal('document'); });
            window.addEventListener('pageshow', function () {
                if (!document.querySelector('.storefront-boot')) signal('ready');
            });
        })();
    </script>
    <style>
        .storefront-boot { display: none; }
        .storefront-js body { margin: 0; }
        .storefront-js .storefront-seo-fallback { display: none; }
        .storefront-js .storefront-boot {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 28px;
            background: #f3f8f5;
        }
        .storefront-boot-recovery { display: block; visibility: hidden; opacity: 0; animation: storefront-recovery-show 0s 12s forwards; text-align: center; max-width: 28rem; padding: 0 24px; color: #263d32; font: 16px/1.6 system-ui, sans-serif; }
        .storefront-boot-recovery a { display: inline-block; padding: 12px 28px; border-radius: 28px; background: #087c3e; color: #fff; text-decoration: none; }
        .storefront-boot-recovery a:focus-visible { outline: 3px solid #263d32; outline-offset: 4px; }
        .storefront-boot-failed .storefront-boot-spinner { display: none; }
        .storefront-boot-failed .storefront-boot-recovery { visibility: visible; opacity: 1; }
        @keyframes storefront-recovery-show { to { visibility: visible; opacity: 1; } }
        .storefront-boot img { width: 148px; height: auto; }
        .storefront-boot-spinner {
            width: 26px;
            height: 26px;
            border: 3px solid #d8e7dd;
            border-top-color: #087c3e;
            border-radius: 50%;
            animation: storefront-boot-spin .8s linear infinite, storefront-spinner-hide 0s 12s forwards;
        }
        @keyframes storefront-boot-spin { to { transform: rotate(360deg); } }
        @keyframes storefront-spinner-hide { to { visibility: hidden; } }
        @media (prefers-reduced-motion: reduce) { .storefront-boot-spinner { animation: none; } }
    </style>

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">

    @include('frontend.seo-head')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">

    @php
        $storefrontDesktopSideSpacing = (int) getSetting('storefrontDesktopSideSpacing', 14);
        $storefrontDesktopSideSpacing = max(0, min(800, $storefrontDesktopSideSpacing));
    @endphp

    <style type="text/css">
        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --storefront-desktop-side-spacing: {{ $storefrontDesktopSideSpacing }}px;
        }
    </style>

    <script>
        window.config = @json($settings);
    </script>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', `${window.config.meta_pixel_id}`);
    </script>
    {{-- Google Tag Manager (Head) --}}
    @if (config('services.gtm_id'))
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ config('services.gtm_id') }}');
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'user_id': '{{ auth()->id() }}',
            'page_type': '{{ Route::currentRouteName() }}'
        });
    </script>

    @endif
    {{-- End Google Tag Manager (Head) --}}
    <noscript>
        <img height="1" width="1" style="display:none"
            src={`https://www.facebook.com/tr?id=${window.config.meta_pixel_id}&ev=PageView&noscript=1`}/>
    </noscript>
    <!-- End Facebook Pixel Code -->


    @viteReactRefresh
    @vite(['resources/frontend/index.css', 'resources/frontend/index.tsx'])
</head>

<body class="antialiased">
    {{-- Google Tag Manager (Body) --}}
    @if (config('services.gtm_id'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.gtm_id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif
    {{-- End Google Tag Manager (Body) --}}
    @php
        // for guests to add to cart
        $tempValue = strtotime('now') . rand(10, 1000);
        $theTime = time() + 86400 * 365;

        if (!isset($_COOKIE['guestUserId'])) {
            setcookie('guestUserId', $tempValue, $theTime, '/'); // 86400 = 1 day
        }
    @endphp

    <div id="app">
        <div class="storefront-boot" role="status" aria-label="Loading store">
            <img src="{{ asset('images/storefront/soumis-logo.png') }}" width="492" height="314" alt="The Soumi’s Can Product Bangladesh">
            <span class="storefront-boot-spinner" aria-hidden="true"></span>
            <div class="storefront-boot-recovery" role="alert">
                <p>The store is taking longer to open. Please check your connection and try again.</p>
                <a href="{{ request()->fullUrlWithQuery(['storefront-retry' => '20261028-2']) }}">Try again</a>
            </div>
        </div>
        <div class="storefront-seo-fallback">@include('frontend.seo-content')</div>
    </div>

    <script>
        function getStrToLangKey(inputString) {
            // Convert to lowercase, replace spaces with underscores
            let modifiedString = inputString.toLowerCase().replace(/ /g, '_');

            // Remove non-alphanumeric characters and underscores
            let sanitizedString = modifiedString.replace(/[^a-z0-9_]/g, '');

            return sanitizedString;
        }
    </script>
</body>

</html>
