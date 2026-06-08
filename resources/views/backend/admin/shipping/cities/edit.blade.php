@extends('layouts.admin')

@section('title')
    {{ translate('Update City') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update City') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.states.index') }}" class="font-bold ">{{ translate('States') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Update City') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-5">
            <div class="card">
                <div class="card__content">
                    <x-backend.forms.city-form :states="$states" :city="$city" :zones="$zones" />
                </div>
            </div>
        </div>
    </div>
@endsection
