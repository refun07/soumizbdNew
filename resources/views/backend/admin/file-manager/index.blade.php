@extends('layouts.admin')

@section('title')
    {{ translate('File Manager') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="flex-grow overflow-y-hidden">
        <!-- start::dashboard breadcrumb -->
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-regular fa-folder"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">
                    {{ translate('Media Library') }}
                </span>
            </div>

            <div class="max-sm:hidden flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
                <span class="text-primary">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <p class="text-muted">{{ translate('File Manager') }}</p>
            </div>
        </div>
        <!-- end::dashboard breadcrumb -->

        <div
            class="show-media-manager bg-background relative md:h-[calc(100vh-241px)] rounded-md shadow-theme [&_.uppy-Dashboard-AddFiles]:h-[280px]">
            <x-file-manager.content />
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        "use strict";
        $(document).ready(async function() {
            mediaManager.data.multiple = true;
            mediaManager.showMedia()
        })
    </script>
@endsection
