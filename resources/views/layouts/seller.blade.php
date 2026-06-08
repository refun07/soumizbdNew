<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">
    <title>@yield('title')</title>

    <!-- css -->
    <x-backend.inc.styles />

    <!-- header js -->
    <script>
        "use strict";
        const ATE = {};
        window.backendApiUrl = "{{ sellerApiUrl() }}";
        window.urlType = "seller";

        // dark / light theme 
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        const setupSystemTheme = () => {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
        setupSystemTheme()

        document.addEventListener('click', function(e) {
            const toggler = e.target.closest('.toggle-theme-btn');
            if (!toggler) return

            var theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark'

            document.documentElement.classList.toggle('dark')
            localStorage.theme = theme
        })
    </script>

</head>

<body class="seller-panel">
    <div class="flex flex-col">
        <!-- start::navbar -->
        <x-backend.inc.seller-navbar />
        <!-- end::navbar -->

        <div class="pt-[10px] h-[calc(100vh-78px)] md:h-[calc(100vh-103px)] flex bg-background-primary-light">
            <div>
                <!-- start::sidebar -->
                <x-backend.inc.seller-sidebar />
                <!-- end::sidebar -->
            </div>

            <!-- start::main content -->
            <div class="flex-grow overflow-y-auto px-4 md:px-8 xl:px-12 pb-[100px] lg:pb-10">
                @yield('content')
            </div>
            <!-- end::main content -->

            <!-- start::modals -->
            <x-backend.modals.confirm />
            <x-backend.modals.order-payments />
            <x-backend.modals.purchase-order-payments />
            <x-backend.modals.customer-assignment />
            <x-backend.modals.purchase-order-payment-histories />
            <x-backend.modals.order-address />
            <x-backend.modals.media-info />
            <!-- end::modals -->

            <!-- start::footer -->
            <x-backend.inc.footer />
            <!-- end::footer -->
        </div>
    </div>

    <!-- file manager modal -->
    <div class="modal micromodal-slide media-manager-modal" id="media-manager-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="media manager">
            <x-file-manager.content />
        </div>
    </div>

    <!-- start::mobile search box -->
    <div class="mobile-search-box">
        <x-backend.inputs.search-input name="search" placeholder="Search for anything" class="navbar-search-input" />

        <div class="navbar-search">
            @include('components.backend.inc.navbar-search', [
                'products' => collect(),
                'orders' => collect(),
            ])
        </div>
    </div>
    <!-- end::mobile search box -->

    <div class="overlay"></div>

    <!-- scripts -->
    <x-backend.inc.scripts />

    <!-- file manager -->
    <x-file-manager.file-manager-js />
    <x-backend.modals.seller-payout-request />

    <!-- from each page -->
    @yield('scripts')
</body>

</html>
