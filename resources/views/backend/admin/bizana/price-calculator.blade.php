@extends('layouts.admin')

@section('title')
    {{ translate('Price Calculator') }} | {{ getSetting('systemName') }}
@endsection

@can('bizana_view_products')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-light fa-calculator"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Price Calculator') }}</span>
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
        @vite(['resources/backend/bizana/price-calculator/page.tsx'])
    @endsection
@endcan