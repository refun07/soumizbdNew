<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">
    <title>@yield('title')</title>

    <!-- css -->
    <x-backend.inc.styles />
    
    @stack('styles')

    <!-- header js -->
    <script>
        "use strict";
        const ATE = {};
        window.backendApiUrl = "{{ adminApiUrl() }}";
        window.urlType = "admin";

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

<body>
    <div class="flex flex-col">
        <!-- start::navbar -->
        <x-backend.inc.navbar />
        <!-- end::navbar -->

        <div class="pt-[10px] h-[calc(100vh-78px)] md:h-[calc(100vh-103px)] flex bg-background-primary-light">
            <div>
                <!-- start::sidebar -->
                <x-backend.inc.sidebar />
                <!-- end::sidebar -->
            </div>

            <!-- start::main content -->
            <div class="flex-grow overflow-y-auto px-4 md:px-8 xl:px-12 pb-[100px] lg:pb-10 @yield('p-class')">
                @yield('content')
            </div>
            <!-- end::main content -->

            <!-- start::modals -->
            <x-backend.modals.confirm />
            <x-backend.modals.variant-conversion-warning />
            <x-backend.modals.order-payments />
            <x-backend.modals.purchase-order-payments />
            <x-backend.modals.customer-assignment />
            <x-backend.modals.purchase-order-payment-histories />
            <x-backend.modals.order-address />
            <x-backend.modals.category-create />
            <x-backend.modals.brand-create />
            <x-backend.modals.unit-create />
            <x-backend.modals.tag-create />
            <x-backend.modals.badge-create />
            <x-backend.modals.seller-payout />
            <x-backend.modals.refund-payment />
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


    <!-- from each page -->
    @yield('scripts')
    <!-- Add Option Modal -->
    <div id="addOptionModal" class="fixed inset-0 z-10 hidden bg-black bg-opacity-50 transition-opacity duration-300" onclick="closeAddOptionModalOnBackdrop(event)">
        <form id="variationValueForm" class="space-y-4" onclick="event.stopPropagation()">
            <div class="h-screen flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg w-full max-w-lg p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Add New Option</h2>
                    <input type="hidden" id="variation_id" name="variation_id" />
                    <input name="name" type="text" id="option_name" placeholder="Enter option name"
                        class="w-full p-2 mb-4 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" />
                    <div id="option-color-selector" class="hidden">
                        <x-backend.inputs.color label="Color Code" name="option_color_code" value=""
                            :isRequired="false" />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAddOptionModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="submitAddOption()"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">
                            Add
                        </button>
                    </div>
                </div>
        </form>
    </div>

    </div>


</body>

</html>
