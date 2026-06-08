@extends('layouts.admin')

@section('title')
    {{ translate('Product Expiry Reports') }} | {{ getSetting('systemName') }}
@endsection

@section('scripts')
<script>
function exportProductExpiryReport() {
    console.log('🔍 Export button clicked!');

    // Get the form
    const form = document.querySelector('form[method="GET"]');
    if (!form) {
        console.error('❌ Form not found');
        return;
    }

    console.log('✅ Form found:', form.action);

    // Create export form
    const exportForm = document.createElement('form');
    exportForm.method = 'POST';
    exportForm.action = '{{ route("admin.productExpiryReports.export") }}';

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    exportForm.appendChild(csrfInput);

    // Copy form data
    const formData = new FormData(form);
    for (const [key, value] of formData.entries()) {
        if (value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            exportForm.appendChild(input);
        }
    }

    console.log('🚀 Submitting export form...');
    document.body.appendChild(exportForm);
    exportForm.submit();

    setTimeout(() => {
        document.body.removeChild(exportForm);
    }, 100);
}

// Debounced search functionality
let searchTimeout;
function setupDebouncedSearch() {
    const searchInput = document.querySelector('.search-input input');
    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            performSearch(e.target.value);
        }, 500); // 500ms delay
    });

    // Also trigger search when other filters change
    const filterSelects = document.querySelectorAll('.filterSelect');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(searchInput.value);
            }, 300);
        });
    });
}

function performSearch(searchValue) {
    const form = document.querySelector('form[method="GET"]');
    if (!form) return;

    // Create URL with current form data
    const formData = new FormData(form);
    const urlParams = new URLSearchParams();

    // Add search value
    if (searchValue.trim()) {
        urlParams.set('search', searchValue.trim());
    }

    // Add other filter values
    for (const [key, value] of formData.entries()) {
        if (key !== 'search' && value) {
            urlParams.set(key, value);
        }
    }

    // Navigate to new URL
    const newUrl = window.location.pathname + '?' + urlParams.toString();
    window.location.href = newUrl;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', setupDebouncedSearch);
</script>
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-clock"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Product Expiry Reports') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Inventory') }}</p>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Product Expiry Reports') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-box text-primary text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ $summary['total_products'] ?? 0 }}</h3>
                    <p class="text-muted">{{ translate('Total Products') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ $summary['expired_count'] ?? 0 }}</h3>
                    <p class="text-muted">{{ translate('Expired Products') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-clock text-orange-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ $summary['expiring_soon_count'] ?? 0 }}</h3>
                    <p class="text-muted">{{ translate('Expiring in 7 Days') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-dollar-sign text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ formatPrice($summary['total_stock_value'] ?? 0) }}</h3>
                    <p class="text-muted">{{ translate('Total Stock Value') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="theme-table">
            <div class="card__title border-none theme-table__filter">
                <form action="" method="GET">
                    <div class="w-full">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 pb-2">
                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="status" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Status') }}" value=""
                                        selected="{{ ($filters['status'] ?? '') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Expired') }}" value="expired"
                                        selected="{{ ($filters['status'] ?? '') == 'expired' ? 'expired' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Expiring Soon') }}" value="expiring-soon"
                                        selected="{{ ($filters['status'] ?? '') == 'expiring-soon' ? 'expiring-soon' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Fresh') }}" value="fresh"
                                        selected="{{ ($filters['status'] ?? '') == 'fresh' ? 'fresh' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="warehouse_id" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Warehouses') }}" value=""
                                        selected="{{ ($filters['warehouse_id'] ?? '') }}" />
                                    @foreach($filterOptions['warehouses'] as $id => $name)
                                        <x-backend.inputs.select-option name="{{ $name }}" value="{{ $id }}"
                                            selected="{{ ($filters['warehouse_id'] ?? '') == $id ? $id : '' }}" />
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="supplier_id" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Suppliers') }}" value=""
                                        selected="{{ ($filters['supplier_id'] ?? '') }}" />
                                    @foreach($filterOptions['suppliers'] as $id => $name)
                                        <x-backend.inputs.select-option name="{{ $name }}" value="{{ $id }}"
                                            selected="{{ ($filters['supplier_id'] ?? '') == $id ? $id : '' }}" />
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="category_id" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Categories') }}" value=""
                                        selected="{{ ($filters['category_id'] ?? '') }}" />
                                    @foreach($filterOptions['categories'] as $id => $name)
                                        <x-backend.inputs.select-option name="{{ $name }}" value="{{ $id }}"
                                            selected="{{ ($filters['category_id'] ?? '') == $id ? $id : '' }}" />
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="sort" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Sort By') }}" value="expiry_date"
                                        selected="{{ ($filters['sort'] ?? 'expiry_date') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Product Name') }}" value="product_name"
                                        selected="{{ ($filters['sort'] ?? '') == 'product_name' ? 'product_name' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Supplier Name') }}" value="supplier_name"
                                        selected="{{ ($filters['sort'] ?? '') == 'supplier_name' ? 'supplier_name' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Warehouse') }}" value="warehouse_name"
                                        selected="{{ ($filters['sort'] ?? '') == 'warehouse_name' ? 'warehouse_name' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Stock Quantity') }}" value="current_stock"
                                        selected="{{ ($filters['sort'] ?? '') == 'current_stock' ? 'current_stock' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Days Until Expiry') }}" value="days_until_expiry"
                                        selected="{{ ($filters['sort'] ?? '') == 'days_until_expiry' ? 'days_until_expiry' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Category') }}" value="category_name"
                                        selected="{{ ($filters['sort'] ?? '') == 'category_name' ? 'category_name' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="sort_dir" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Ascending') }}" value="asc"
                                        selected="{{ ($filters['sort_dir'] ?? 'asc') == 'asc' ? 'asc' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Descending') }}" value="desc"
                                        selected="{{ ($filters['sort_dir'] ?? '') == 'desc' ? 'desc' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-2 xl:col-span-8">
                                <x-backend.inputs.search-input name="search"
                                    placeholder="{{ translate('Search by product name, SKU, supplier, warehouse...') }}"
                                    value="{{ $filters['search'] ?? '' }}"
                                    class="search-input w-full" />
                            </div>

                            <div class="sm:col-span-2 xl:col-span-4 flex flex-col sm:flex-row gap-3 xl:justify-end">
                                <button type="submit"
                                        class="flex items-center justify-center gap-2 px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary/90 transition-colors w-full sm:w-auto xl:w-[180px] min-h-[42px]">
                                    <span class="text-sm">
                                        <i class="fas fa-filter"></i>
                                    </span>
                                    <span>{{ translate('Filter') }}</span>
                                </button>

                                @can('view_reports')
                                    <button id="exportPdfBtn"
                                            type="button"
                                            class="flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/80 transition-colors w-full sm:w-auto xl:w-[180px] min-h-[42px]"
                                            onclick="exportProductExpiryReport()">
                                        <span class="text-sm">
                                            <i class="fas fa-file-pdf"></i>
                                        </span>
                                        <span>{{ translate('Export PDF') }}</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <table class="product-expiry-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            {{ translate('Product Information') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('SKU') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Expiry Date') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Days Until Expiry') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Current Stock') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Supplier') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Warehouse') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Stock Value') }}
                        </th>
                        <th class="text-center">
                            {{ translate('Status') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stocks as $stock)
                        <tr>
                            <td>
                                <div class="max-w-[250px]">
                                    <div class="font-semibold text-foreground line-clamp-2">
                                        {{ $stock['product_name'] }}
                                    </div>
                                    @if(!empty($stock['variation_title']))
                                        <div class="text-sm text-muted">
                                            {{ $stock['variation_title'] }}
                                        </div>
                                    @endif
                                    @if(!empty($stock['category_name']))
                                        <div class="text-xs text-muted">
                                            <i class="fa-regular fa-folder mr-1"></i>
                                            {{ $stock['category_name'] }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="font-mono text-sm">
                                    {{ $stock['sku'] ?: 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="font-medium">{{ $stock['expiry_date_formatted'] }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    @if($stock['days_until_expiry'] < 0)
                                        <span class="text-red-600 font-medium">
                                            {{ abs($stock['days_until_expiry']) }} {{ translate('days ago') }}
                                        </span>
                                    @else
                                        <span class="text-green-600 font-medium">
                                            {{ $stock['days_until_expiry'] }} {{ translate('days') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="font-medium">{{ $stock['current_stock'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="font-medium">{{ $stock['supplier_name'] }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div class="font-medium">{{ $stock['warehouse_name'] }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-foreground">
                                    {{ formatPrice($stock['total_value'] ?? 0) }}
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeInfo = \App\Services\ProductExpiryReportService::getExpiryBadgeInfo($stock['status']);
                                @endphp
                                <span class="badge {{ $badgeInfo['class'] }}">
                                    <i class="{{ $badgeInfo['icon'] }} mr-1"></i>
                                    {{ $badgeInfo['text'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="text-muted">
                                    <i class="fa-regular fa-inbox text-4xl mb-3"></i>
                                    <p>{{ translate('No products with expiry dates found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($stocks->hasPages())
                <div class="card__footer flex justify-between items-center">
                    <div class="text-muted text-sm">
                        {{ translate('Showing') }} {{ $stocks->firstItem() }} {{ translate('to') }} {{ $stocks->lastItem() }}
                        {{ translate('of') }} {{ $stocks->total() }} {{ translate('results') }}
                    </div>
                    {{ $stocks->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection
