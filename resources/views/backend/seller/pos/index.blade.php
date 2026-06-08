@extends('layouts.seller')

@section('title')
    {{ translate('POS') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/pos/index.tsx'])
@endsection
