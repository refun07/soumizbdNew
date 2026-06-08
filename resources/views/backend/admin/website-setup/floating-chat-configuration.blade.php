@extends('layouts.admin')

@section('title')
    {{ translate('Floating Chat Configuration') }} | {{ getSetting('systemName') }}
@endsection

@can('homepage')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-regular fa-comments"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Floating Chat Configuration') }}</span>
            </div>

            <div class="max-sm:hidden flex items-center gap-2">
                <a href="{{ route('admin.theme.selection') }}" class="font-bold ">{{ translate('Theme Selection') }}</a>
                <span class="text-primary dark:text-muted">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <p class="text-muted">{{ translate('Floating Chat Configuration') }}</p>
            </div>
        </div>

        <div id="floating-chat-configuration-app"></div>
    @endsection

    @section('scripts')
        <script>
            window.floatingChatConfig = {
                apiPrefix: '/admin/api/floating-chat'
            };
        </script>

        @viteReactRefresh
        @vite(['resources/backend/website-setup/floating-chat-configuration.tsx'])
    @endsection
@endcan
