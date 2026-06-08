@extends('layouts.seller')

@section('title')
    {{ translate('Dashboard') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-light fa-chart-mixed"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Seller Dashboard') }}</span>
        </div>
    </div>

    <div id="app"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>
    @viteReactRefresh
    @vite(['resources/backend/seller/index.tsx'])
@endsection
