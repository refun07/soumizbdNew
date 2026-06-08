@extends('layouts.admin')

@section('title')
    {{ translate('Stock Management') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-boxes"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Management') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Management') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card__content">
            <form method="GET" action="{{ route('admin.stocks.index') }}">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-4">
                        <x-backend.inputs.text label="Search Product" :labelInline="false" name="search"
                            value="{{ request('search') }}" placeholder="Search by product name" />
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <x-backend.inputs.select label="Warehouse" :labelInline="false" name="warehouse_id" data-search="false">
                            <x-backend.inputs.select-option name="All Warehouses" value="" />
                            @foreach ($warehouses as $warehouse)
                                <x-backend.inputs.select-option name="{{ $warehouse->name }}" value="{{ $warehouse->id }}"
                                    selected="{{ request('warehouse_id') == $warehouse->id }}" />
                            @endforeach
                        </x-backend.inputs.select>
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <x-backend.inputs.select label="Expiry Status" :labelInline="false" name="expiry_status" data-search="false">
                            <x-backend.inputs.select-option name="All Status" value="" />
                            <x-backend.inputs.select-option name="Fresh" value="fresh"
                                selected="{{ request('expiry_status') == 'fresh' }}" />
                            <x-backend.inputs.select-option name="Expiring Soon" value="expiring-soon"
                                selected="{{ request('expiry_status') == 'expiring-soon' }}" />
                            <x-backend.inputs.select-option name="Expired" value="expired"
                                selected="{{ request('expiry_status') == 'expired' }}" />
                            <x-backend.inputs.select-option name="No Expiry" value="no-expiry"
                                selected="{{ request('expiry_status') == 'no-expiry' }}" />
                        </x-backend.inputs.select>
                    </div>
                    <div class="col-span-12 md:col-span-2 flex items-end">
                        <x-backend.inputs.button buttonText="Filter" type="submit" />
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-12 gap-4 mb-6">
        <div class="col-span-12 md:col-span-4">
            <a href="{{ route('admin.stocks.expiry-report') }}"
               class="block p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-red-800">{{ translate('Expired Stock Report') }}</h3>
                        <p class="text-sm text-red-600">{{ translate('View all expired items') }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-span-12 md:col-span-4">
            <a href="{{ route('admin.stocks.expiring-soon-report') }}"
               class="block p-4 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-orange-800">{{ translate('Expiring Soon Report') }}</h3>
                        <p class="text-sm text-orange-600">{{ translate('Items expiring in 30 days') }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-span-12 md:col-span-4">
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800">{{ translate('FIFO Principle') }}</h3>
                        <p class="text-sm text-blue-600">{{ translate('First-In-First-Out stock rotation') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock List -->
    <div class="card">
        <div class="card__content">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-primary/10 border-b border-border">
                        <tr class="text-left">
                            <th class="p-4 ps-6">{{ translate('Product') }}</th>
                            <th class="p-4">{{ translate('Warehouse') }}</th>
                            <th class="p-4">{{ translate('Quantity') }}</th>
                            <th class="p-4">{{ translate('Purchase Cost') }}</th>
                            <th class="p-4">{{ translate('Expiry Date') }}</th>
                            <th class="p-4">{{ translate('Status') }}</th>
                            <th class="p-4">{{ translate('Added Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($stocks->count() > 0)
                            @foreach ($stocks as $stock)
                                <tr class="bg-background border-b border-border hover:bg-background-hover">
                                    <td class="p-4">
                                        <div>
                                            <div class="font-medium">{{ $stock->productVariation->product->collectTranslation('name') }}</div>
                                            @if ($stock->productVariation->product->has_variation)
                                                <div class="text-sm text-muted">{{ generateVariationName($stock->productVariation->code) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4">{{ $stock->warehouse->name }}</td>
                                    <td class="p-4">
                                        <span class="font-semibold">{{ $stock->qty }}</span>
                                        <span class="text-muted">{{ $stock->productVariation->product->unit?->collectTranslation('name') }}</span>
                                    </td>
                                    <td class="p-4">{{ formatPrice($stock->purchase_cost) }}</td>
                                    <td class="p-4">
                                        @if ($stock->expiry_date)
                                            {{ date('Y-m-d', $stock->expiry_date) }}
                                        @else
                                            <span class="text-muted">{{ translate('No expiry') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <x-backend.elements.expiry-badge
                                            :expiryDate="$stock->expiry_date ? date('Y-m-d', $stock->expiry_date) : null"
                                            size="sm" />
                                    </td>
                                    <td class="p-4">{{ $stock->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="p-8 text-center text-muted">
                                    {{ translate('No stock entries found') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($stocks->hasPages())
                <div class="mt-6">
                    {{ $stocks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection