@extends('layouts.admin')

@section('title')
    {{ translate('Return Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/return-details/index.tsx'])
@endsection
