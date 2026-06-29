<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">

    <title>{{ getSetting('systemName') }}</title>

    <meta name="description" content="@yield('meta_description', $meta['meta_description'])" />
    <meta name="keywords" content="@yield('meta_keywords', $meta['meta_keywords'])">

    @yield('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta['meta_title'] }}">
    <meta itemprop="description" content="{{ $meta['meta_description'] }}">
    <meta itemprop="image" content="{{ $meta['meta_image'] }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $meta['meta_title'] }}">
    <meta name="twitter:description" content="{{ $meta['meta_description'] }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ $meta['meta_image'] }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta['meta_title'] }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $meta['meta_url'] }}" />
    <meta property="og:image" content="{{ $meta['meta_image'] }}" />
    <meta property="og:description" content="{{ $meta['meta_description'] }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

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

    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="app"></div>

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
