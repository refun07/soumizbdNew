@extends('layouts.admin')

@section('title')
    {{ translate('Pre Orders') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/react/pages/index.tsx'])
@endsection
