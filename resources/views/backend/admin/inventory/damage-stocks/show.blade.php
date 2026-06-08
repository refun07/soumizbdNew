@extends('layouts.admin')

@section('title')
    {{ translate('Damage Stock Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Damage Stock Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.damageStocks.index') }}" class="font-bold ">{{ translate('Damage Stocks') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Damage Stock Details') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card__content">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-6">
                    <div class="theme-input-label">{{ translate('Product') }}</div>
                    <p class="text-sm text-foreground">
                        {{ $damageStock->productVariation?->product?->collectTranslation('name') }}
                        @if ($damageStock->productVariation?->product?->has_variation)
                            - <span class="text-orange-500 font-bold">{{ $damageStock->productVariation?->name }}</span>
                        @endif
                    </p>
                </div>
                @if (useInventory())
                    <div class="col-span-12 md:col-span-6">
                        <div class="theme-input-label">{{ translate('Warehouse') }}</div>
                        <p class="text-sm text-foreground">{{ $damageStock->warehouse?->name ?? translate('N/A') }}</p>
                    </div>
                @endif
                <div class="col-span-12 md:col-span-6">
                    <div class="theme-input-label">{{ translate('Quantity') }}</div>
                    <p class="text-sm text-foreground">{{ $damageStock->quantity }}</p>
                </div>
                <div class="col-span-12 md:col-span-6">
                    <div class="theme-input-label">{{ translate('Damage Type') }}</div>
                    <p class="text-sm text-foreground">{{ translate(ucfirst(str_replace('_', ' ', $damageStock->damage_type))) }}</p>
                </div>
                <div class="col-span-12 md:col-span-6">
                    <div class="theme-input-label">{{ translate('Damage Date') }}</div>
                    <p class="text-sm text-foreground">{{ date('d M, Y', strtotime($damageStock->return_date)) }}</p>
                </div>
                <div class="col-span-12 md:col-span-6">
                    <div class="theme-input-label">{{ translate('Recorded By') }}</div>
                    <p class="text-sm text-foreground">{{ $damageStock->creator?->name ?? translate('System') }}</p>
                </div>
                @if ($damageStock->reason)
                    <div class="col-span-12">
                        <div class="theme-input-label">{{ translate('Reason') }}</div>
                        <p class="text-sm text-foreground">{{ $damageStock->reason }}</p>
                    </div>
                @endif
                @if ($damageStock->notes)
                    <div class="col-span-12">
                        <div class="theme-input-label">{{ translate('Notes') }}</div>
                        <p class="text-sm text-foreground">{{ $damageStock->notes }}</p>
                    </div>
                @endif
                @if ($damageStock->returnRequest)
                    <div class="col-span-12">
                        <div class="theme-input-label">{{ translate('Source') }}</div>
                        <p class="text-sm text-foreground">
                            {{ translate('From Return Request #') }}{{ $damageStock->returnRequest->id }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
