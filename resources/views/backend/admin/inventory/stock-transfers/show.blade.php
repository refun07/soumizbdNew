@extends('layouts.admin')

@section('title')
    {{ translate('Stock Tranfer Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Tranfer Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.stockTransfers.index') }}" class="font-bold ">{{ translate('Stock Tranfers') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Tranfer Details') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="card__content">
            <x-backend.forms.stock-transfer-form :warehouses="$warehouses" :stockTransfer="$stockTransfer" />
        </div>
    </div>
@endsection

@section('scripts')
    <x-backend.inc.stock-transfers-scripts />
@endsection
