@extends('layouts.admin')

@section('title')
    {{ translate('Landing Pages') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div id="landing-page-listing" class="admin-container"></div>
@endsection

@section('scripts')
    @viteReactRefresh
    @vite(['resources/backend/website-setup/landing-pages.tsx'])
@endsection