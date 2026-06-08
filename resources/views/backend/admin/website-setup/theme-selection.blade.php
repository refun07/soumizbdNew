@extends('layouts.admin')

@section('title')
    {{ translate('Improved Theme Selection') }} | {{ getSetting('systemName') }}
@endsection

@can('homepage')
    @section('content')
        {{-- breadcrumb starts --}}
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-regular fa-palette"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Improved Theme Selection') }}</span>
            </div>

            <div class="max-sm:hidden flex items-center gap-2">
                <a href="#" class="font-bold ">{{ translate('Website Setup') }}</a>
                <span class="text-primary dark:text-muted">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <p class="text-muted">{{ translate('Improved Theme Selection') }}</p>
            </div>
        </div>
        {{-- breadcrumb ends --}}

        <div id="theme-selection-app"></div>
    @endsection

    @section('scripts')
        <script>
            window.themeSelectionConfig = {
                apiPrefix: '/admin/api/theme-selection'
            };
        </script>

        @viteReactRefresh
        @vite(['resources/backend/website-setup/theme-selection.tsx'])
    @endsection
@endcan
