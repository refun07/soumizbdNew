@extends('layouts.seller')

@section('title')
    {{ translate('Stock Adjustment Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Adjustment Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.stockAdjustments.index') }}" class="font-bold ">{{ translate('Stock Adjustments') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Adjustment Details') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="card__content">
            <x-backend.forms.stock-adjustment-form :warehouses="$warehouses" :suppliers="$suppliers" :stockAdjustment="$stockAdjustment" />
        </div>
    </div>
@endsection

@section('scripts')
    <x-backend.inc.stock-adjustments-scripts />
@endsection
