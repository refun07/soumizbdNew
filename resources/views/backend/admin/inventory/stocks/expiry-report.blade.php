@extends('layouts.admin')

@section('title')
    {{ translate('Expired Stock Report') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-exclamation-triangle"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Expired Stock Report') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.stocks.index') }}" class="font-bold ">{{ translate('Stock Management') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Expired Stock Report') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card__content">
            <form method="GET" action="{{ route('admin.stocks.expiry-report') }}">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-4">
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
            <div class="card border-red-200 bg-red-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-red-800">{{ $report['total_expired_items'] }}</h3>
                            <p class="text-red-600">{{ translate('Expired Items') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-4">
            <div class="card border-red-200 bg-red-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-red-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-red-800">{{ $report['total_expired_quantity'] }}</h3>
                            <p class="text-red-600">{{ translate('Total Quantity') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12 md:col-span-4">
            <div class="card border-red-200 bg-red-50">
                <div class="card__content">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-red-500 text-3xl mr-4"></i>
                        <div>
                            <h3 class="text-2xl font-bold text-red-800">{{ formatPrice($report['total_value']) }}</h3>
                            <p class="text-red-600">{{ translate('Total Value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expired Stock List -->
    <div class="card">
        <div class="card__content">
            @if ($report['total_expired_items'] > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-red-50 border-b border-red-200">
                            <tr class="text-left">
                                <th class="p-4 ps-6">{{ translate('Product') }}</th>
                                <th class="p-4">{{ translate('Warehouse') }}</th>
                                <th class="p-4">{{ translate('Quantity') }}</th>
                                <th class="p-4">{{ translate('Expiry Date') }}</th>
                                <th class="p-4">{{ translate('Days Expired') }}</th>
                                <th class="p-4">{{ translate('Value') }}</th>
                                <th class="p-4">{{ translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report['items'] as $item)
                                <tr class="bg-background border-b border-border hover:bg-red-50">
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
                                        <span class="font-semibold text-red-600">{{ $item['quantity'] }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-red-600 font-medium">{{ $item['expiry_date'] }}</span>
                                    </td>
                                    <td class="p-4">
                                        <x-backend.elements.expiry-badge expiry-date="{{ $item['expiry_date'] }}" size="sm" />
                                    </td>
                                    <td class="p-4">{{ formatPrice($item['value']) }}</td>
                                    <td class="p-4">
                                        <button type="button" class="text-red-600 hover:text-red-800 font-medium"
                                                onclick="confirmRemoveExpiredStock({{ $item['id'] }})">
                                            {{ translate('Remove') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-800 mb-2">{{ translate('No Expired Stock') }}</h3>
                    <p class="text-green-600">{{ translate('Great! There are no expired items in your inventory.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="removeExpiredStockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-background">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ translate('Remove Expired Stock') }}</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        {{ translate('Are you sure you want to remove this expired stock entry? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmRemoveBtn"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600">
                        {{ translate('Remove') }}
                    </button>
                    <button id="cancelRemoveBtn"
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600">
                        {{ translate('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let stockIdToRemove = null;

    function confirmRemoveExpiredStock(stockId) {
        stockIdToRemove = stockId;
        document.getElementById('removeExpiredStockModal').classList.remove('hidden');
    }

    document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
        if (stockIdToRemove) {
            // Implement AJAX call to remove expired stock
            fetch(`/admin/stocks/${stockIdToRemove}/remove-expired`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong');
            });
        }
        closeModal();
    });

    document.getElementById('cancelRemoveBtn').addEventListener('click', closeModal);

    function closeModal() {
        document.getElementById('removeExpiredStockModal').classList.add('hidden');
        stockIdToRemove = null;
    }
</script>
@endpush