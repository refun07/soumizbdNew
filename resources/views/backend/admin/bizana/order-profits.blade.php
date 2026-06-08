@extends('layouts.admin')

@section('title')
    {{ translate('Business Analytics - Order Profits') }} | {{ getSetting('systemName') }}
@endsection

@can('bizana_view_profits')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-light fa-chart-column"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Business Analytics - Order Profits') }}</span>
            </div>
        </div>

        <div id="bizana-app"></div>
    @endsection

    @section('scripts')
        <script>
            window.config = @json($settings ?? []);
            window.bizanaConfig = {
                section: 'order-profits',
                adminMode: true,
                apiPrefix: '/admin/api/bizana'
            };
        </script>

        @viteReactRefresh
        @vite(['resources/backend/bizana/order-profits/page.tsx'])
    @endsection
@endcan
