@extends('layouts.admin')

@section('title')
    {{ translate('Update Unit') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Unit') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.units.index') }}" class="font-bold ">{{ translate('Unit List') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Update') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-4">
            <div class="card">
                <div class="card__content">
                    <div class="flex justify-end mb-3">
                        <x-backend.inputs.change-languages langKey="{{ $lang_key }}" />
                    </div>
                    <x-backend.forms.unit-form :unit="$unit" langKey="{{ $lang_key }}" />
                </div>
            </div>
        </div>
    </div>
@endsection
