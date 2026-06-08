@extends('layouts.admin')

@section('title')
    {{ translate('Color & Branding Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Color & Branding Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Website Setup') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Settings') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- logo starts --}}
    <div class="card max-w-[900px] mt-5">
        <h4 class="card__title">{{ translate('Logo, Icon & More') }}</h4>

        <div class="card__content">
            <x-backend.forms.settings.logo-icon-form />
        </div>
    </div>
    {{-- logo ends --}}
@endsection
