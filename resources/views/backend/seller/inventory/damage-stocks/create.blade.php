@extends('layouts.seller')

@section('title')
    {{ translate('Create Damage Stock') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Create Damage Stock') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.damageStocks.index') }}" class="font-bold ">{{ translate('Damage Stocks') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Create Damage Stock') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card__content">
            <x-backend.forms.damage-stock-form :warehouses="$warehouses" :defaultWarehouseId="$defaultWarehouseId ?? null" />
        </div>
    </div>
@endsection

@section('scripts')
    <x-backend.inc.damage-stocks-scripts />
@endsection
