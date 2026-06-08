@extends('layouts.seller')

@section('title')
    {{ translate('Chats') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-light fa-envelope"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Chats') }}</span>
        </div>
    </div>

    <div id="app"></div>
@endsection

@section('scripts')
    <script>
        "use strict";
        window.userId = "{{ userId() }}"
    </script>

    @viteReactRefresh
    @vite(['resources/backend/seller-chat/index.tsx'])
@endsection
