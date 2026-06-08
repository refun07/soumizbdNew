@extends('layouts.admin')

@section('title')
    {{ translate('Support Tickets') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/support/index.tsx'])
@endsection
