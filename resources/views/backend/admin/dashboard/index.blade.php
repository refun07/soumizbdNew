@extends('layouts.admin')

@section('title')
    {{ translate('Dashboard') }} | {{ getSetting('systemName') }}
@endsection

@can('show_dashboard')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-light fa-chart-mixed"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Admin Dashboard') }}</span>
            </div>
        </div>

        <div id="app"></div>
    @endsection

    @section('scripts')
        <script>
            window.config = @json($settings);
            window.currentLocale = '{{ Session::get('locale', 'en-US') }}';
        </script>

        @viteReactRefresh
        @vite(['resources/backend/dashboard/index.tsx'])
    @endsection
@endcan
