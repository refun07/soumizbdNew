@extends('layouts.admin')

@section('title')
    {{ translate('Update Category') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Category') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.categories.index') }}" class="font-bold ">{{ translate('Category List') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Update') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-7">
            <div class="card">
                <div class="card__content">
                    <div class="flex justify-end mb-3">
                        <x-backend.inputs.change-languages langKey="{{ $lang_key }}" />
                    </div>
                    <x-backend.forms.category-form :category="$category" langKey="{{ $lang_key }}" :categories="$categories"
                        :variations="$variations" />
                </div>
            </div>
        </div>
    </div>
@endsection
