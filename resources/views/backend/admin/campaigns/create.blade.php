@extends('layouts.admin')

@section('title')
    {{ translate('Add New Campaign') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Campaign') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.campaigns.index') }}" class="font-bold ">{{ translate('Campaigns') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Campaign') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <x-backend.forms.campaign-form />
@endsection
