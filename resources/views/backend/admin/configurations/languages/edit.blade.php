@extends('layouts.admin')

@section('title')
    {{ translate('Languages') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Languages') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Configurations') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Languages') }}</p>
        </div>
    </div>

    <div class="card max-w-[700px]">
        <h4 class="card__title">{{ translate('Update Language') }}</h4>

        <div class="card__content">
            <x-backend.forms.language-form :language="$language" />
        </div>
    </div>
@endsection
