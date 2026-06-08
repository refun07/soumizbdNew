@extends('layouts.admin')

@section('title')
    {{ translate('Daily Expenses') }} | {{ getSetting('systemName') }}
@endsection

@can('bizana_manage_accounting')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-light fa-calendar-days"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Daily Expenses') }}</span>
            </div>
        </div>

        <div id="bizana-app"></div>
    @endsection

    @section('scripts')
        <script>
            window.config = @json($settings ?? []);
            window.bizanaConfig = {
                apiPrefix: '/admin/api/bizana'
            };
        </script>

        @viteReactRefresh
        @vite(['resources/backend/bizana/daily-expenses/page.tsx'])
    @endsection
@endcan