@extends('layouts.admin')

@section('title')
    {{ translate('My Commissions') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="agent-commissions-app" class="bg-background-primary-light"
        data-balance="{{ json_encode($balance) }}"
        data-agent-id="{{ auth()->user()->id }}">
    </div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings ?? []);
        window.currentLocale = '{{ Session::get('locale', 'en-US') }}';
    </script>

    @viteReactRefresh
    @vite(['resources/backend/agent-commissions/index.tsx'])
@endsection
