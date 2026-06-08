@extends('layouts.seller')

@section('title')
    {{ translate('Add New Supplier') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Supplier') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.suppliers.index') }}" class="font-bold ">{{ translate('Suppliers') }}</a>
            <span class="text-theme-indigo">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Supplier') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card max-w-[900px]">
        <div class="card__content">
            <x-backend.forms.supplier-form />
        </div>
    </div>
@endsection
