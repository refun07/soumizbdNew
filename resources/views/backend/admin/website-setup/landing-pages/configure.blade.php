<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">
    <title>{{ translate('Update Landing Page') }} | {{ getSetting('systemName') }}</title>

    <!-- css -->
    <x-backend.inc.styles />

    <!-- header js -->
    <script>
        "use strict";
        const ATE = {};
        window.backendApiUrl = "{{ adminApiUrl() }}";
        window.urlType = "admin";

        // dark / light theme - inherit from admin panel
        const setupSystemTheme = () => {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
        setupSystemTheme()
    </script>

    <style>
        /* Full-screen builder reset - override admin panel defaults */
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }
        
        body {
            overflow: visible !important;
            height: auto !important;
            min-height: 100vh;
        }
        
        #landing-page-configure {
            min-height: 100vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="landing-page-configure"></div>

    <!-- scripts -->
    <x-backend.inc.scripts />

    <!-- from each page -->
    @viteReactRefresh
    @vite(['resources/backend/website-setup/landing-pages.tsx'])
</body>
</html>
