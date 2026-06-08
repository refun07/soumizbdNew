@extends('layouts.admin')

@section('title')
    {{ translate('Header Configuration') }} | {{ getSetting('systemName') }}
@endsection

@can('homepage')
    @section('content')
        {{-- breadcrumb starts --}}
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-regular fa-sliders-h"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Header Configuration') }}</span>
            </div>

            <div class="max-sm:hidden flex items-center gap-2">
                <a href="{{ route('admin.theme.selection') }}" class="font-bold ">{{ translate('Theme Selection') }}</a>
                <span class="text-primary dark:text-muted">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <p class="text-muted">{{ translate('Header Configuration') }}</p>
            </div>
        </div>
        {{-- breadcrumb ends --}}

        <div id="header-configuration-app"></div>
    @endsection

    @section('scripts')
        <script>
            window.headerConfigurationConfig = {
                headerId: '{{ $headerId }}',
                apiPrefix: '/admin/api/theme-selection'
            };
        </script>

        @viteReactRefresh
        @vite(['resources/backend/website-setup/header-configuration.tsx'])
    @endsection
@endcan