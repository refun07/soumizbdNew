@extends('layouts.admin')

@section('title')
    {{ translate('Add New Category') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Category') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.categories.index') }}" class="font-bold ">{{ translate('Categories') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Category') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card max-w-[900px]">
        <div class="card__content">
            <x-backend.forms.category-form :categories="$categories" :variations="$variations" />
        </div>
    </div>
@endsection
