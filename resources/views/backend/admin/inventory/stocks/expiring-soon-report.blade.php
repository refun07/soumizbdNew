@extends('layouts.admin')

@section('title')
    {{ translate('Expiring Soon Report') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-clock"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Expiring Soon Report') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.stocks.index') }}" class="font-bold ">{{ translate('Stock Management') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Expiring Soon Report') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card__content">
            <form method="GET" action="{{ route('admin.stocks.expiring-soon-report') }}">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-3">
                        <x-backend.inputs.number label="Days Ahead" :labelInline="false" name="days"
                            value="{{ $days }}" min="1" max="365" />
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
                    <div class="col-span-12 md:col-span-2 flex items-end">
                        <x-backend.inputs.button buttonText="Filter" type="submit" />
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-12 gap-4 mb-6">
        <div class="col-span-12 md:col-span-4">
            <div class="card border-orange-200 bg-orange-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-orange-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-orange-800">{{ $report['total_expiring_items'] }}</h3>
                            <p class="text-orange-600">{{ translate('Items Expiring') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-4">
            <div class="card border-orange-200 bg-orange-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-orange-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-orange-800">{{ $report['total_expiring_quantity'] }}</h3>
                            <p class="text-orange-600">{{ translate('Total Quantity') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-4">
            <div class="card border-orange-200 bg-orange-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-orange-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-orange-800">{{ formatPrice($report['total_value']) }}</h3>
                            <p class="text-orange-600">{{ translate('Total Value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notice -->
    <div class="card border-orange-200 bg-orange-50 mb-6">
        <div class="card__content">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-orange-500 text-xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-orange-800">{{ translate('Action Required') }}</h3>
                    <p class="text-orange-600">
                        {{ translate('These items are expiring within the next :days days. Consider running promotions, discounts, or returning to suppliers to minimize losses.', ['days' => $days]) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Stock List -->
    <div class="card">
        <div class="card__content">
            @if ($report['total_expiring_items'] > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-orange-50 border-b border-orange-200">
                            <tr class="text-left">
                                <th class="p-4 ps-6">{{ translate('Product') }}</th>
                                <th class="p-4">{{ translate('Warehouse') }}</th>
                                <th class="p-4">{{ translate('Quantity') }}</th>
                                <th class="p-4">{{ translate('Expiry Date') }}</th>
                                <th class="p-4">{{ translate('Days Until Expiry') }}</th>
                                <th class="p-4">{{ translate('Status') }}</th>
                                <th class="p-4">{{ translate('Value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['items'] as $item)
                                <tr class="bg-background border-b border-border hover:bg-orange-50">
                                    <td class="p-4">
                                        <div>
                                            <div class="font-medium">{{ $item['product_name'] }}</div>
                                            @if ($item['variation'])
                                                <div class="text-sm text-muted">{{ generateVariationName($item['variation']) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4">{{ $item['warehouse'] }}</td>
                                    <td class="p-4">
                                        <span class="font-semibold text-orange-600">{{ $item['quantity'] }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="font-medium">{{ $item['expiry_date'] }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if ($item['days_until_expiry'] <= 7)
                                                bg-red-100 text-red-800
                                            @elseif ($item['days_until_expiry'] <= 14)
                                                bg-orange-100 text-orange-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ $item['days_until_expiry'] }} {{ translate('days') }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <x-backend.elements.expiry-badge
                                            expiry-date="{{ $item['expiry_date'] }}"
                                            size="sm" />
                                    </td>
                                    <td class="p-4">{{ formatPrice($item['value']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-800 mb-2">{{ translate('No Items Expiring Soon') }}</h3>
                    <p class="text-green-600">{{ translate('Great! There are no items expiring within the next :days days.', ['days' => $days]) }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection