@extends('layouts.admin')

@section('title')
    {{ translate('IP Blocker') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="app" class="bg-background-primary-light"
        data-initial-data="{{ json_encode($blockedIps ?? []) }}"
        data-settings="{{ json_encode($settings ?? []) }}">
    </div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings ?? []);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/react/pages/index.tsx'])
@endsection