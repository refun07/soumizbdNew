@extends('layouts.admin')

@section('title')
    {{ translate('Cart List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"
        data-initial-carts="{{ json_encode($carts) }}"
        data-filter-data="{{ json_encode($filterData) }}">
    </div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings ?? []);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/cart-list/index.tsx'])
@endsection
