@extends('layouts.admin')

@section('title')
    {{ translate('Stock Reports') }} | {{ getSetting('systemName') }}
@endsection

@push('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-icon.blue {
        background-color: rgba(59, 130, 246, 0.1);
        color: rgb(59, 130, 246);
    }
    .stat-icon.green {
        background-color: rgba(34, 197, 94, 0.1);
        color: rgb(34, 197, 94);
    }
    .stat-icon.orange {
        background-color: rgba(249, 115, 22, 0.1);
        color: rgb(249, 115, 22);
    }
    .stat-icon.red {
        background-color: rgba(239, 68, 68, 0.1);
        color: rgb(239, 68, 68);
    }
    
    /* Expandable Row Styles */
    .stock-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .stock-row:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }
    .stock-row.expanded {
        background-color: rgba(59, 130, 246, 0.08);
    }
    
    .expand-icon {
        transition: transform 0.3s ease;
    }
    .stock-row.expanded .expand-icon {
        transform: rotate(180deg);
    }
    
    /* Variation panel row styles */
    .variation-panel-row {
        display: none;
    }
    
    .variation-panel-row.expanded {
        display: table-row;
    }
    
    .variation-panel-row td {
        border: none !important;
        padding: 0 !important;
    }
    
    .variation-panel {
        width: 100%;
    }
    
    /* Table styling to match footable */
    .product-list-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .product-list-table thead {
        text-transform: uppercase;
        text-align: left;
        background-color: rgba(var(--color-primary-rgb), 0.05);
    }
    
    .product-list-table thead th {
        padding: 0.5rem;
        vertical-align: middle;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        border: none;
        font-weight: 500;
        background-color: rgba(var(--color-primary-rgb), 0.05);
        border-radius: 0;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    @media (min-width: 1024px) {
        .product-list-table thead th {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
    }
    
    .product-list-table thead th:first-child {
        padding-left: 1rem;
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    
    @media (min-width: 1024px) {
        .product-list-table thead th:first-child {
            padding-left: 3rem;
        }
    }
    
    .product-list-table thead th:last-child {
        padding-right: 1rem;
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    @media (min-width: 1024px) {
        .product-list-table thead th:last-child {
            padding-right: 3rem;
        }
    }
    
    .product-list-table tbody td {
        padding: 0.5rem;
        border-bottom: 1px solid var(--color-border);
        vertical-align: middle;
        font-weight: 500;
    }
    
    @media (min-width: 1024px) {
        .product-list-table tbody td {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
    }
    
    .product-list-table tbody td:first-child {
        padding-left: 1rem;
    }
    
    @media (min-width: 1024px) {
        .product-list-table tbody td:first-child {
            padding-left: 3rem;
        }
    }
    
    .product-list-table tbody td:last-child {
        padding-right: 1rem;
    }
    
    @media (min-width: 1024px) {
        .product-list-table tbody td:last-child {
            padding-right: 3rem;
        }
    }
    
    .product-list-table tbody {
        border-top: 1px solid var(--color-border);
    }
    
    /* Center product image column */
    .product-list-table tbody td:nth-child(2) {
        text-align: center;
    }
    
    .product-list-table tbody td:nth-child(2) img {
        margin: 0 auto;
    }
    
    .variation-panel {
        display: none;
    }
    .variation-panel.show {
        display: block;
    }
    
    .variation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1rem 1.5rem;
    }
    
    .variation-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .variation-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    /* Stock Level Indicators */
    .stock-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .stock-high {
        background-color: rgba(34, 197, 94, 0.1);
        color: rgb(22, 163, 74);
    }
    .stock-medium {
        background-color: rgba(234, 179, 8, 0.1);
        color: rgb(202, 138, 4);
    }
    .stock-low {
        background-color: rgba(239, 68, 68, 0.1);
        color: rgb(220, 38, 38);
    }
    
    /* Progress Bar */
    .stock-progress {
        height: 6px;
        background-color: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
        margin-top: 0.25rem;
    }
    .stock-progress-bar {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.3s ease;
    }
    .stock-progress-bar.high {
        background: linear-gradient(90deg, rgb(34, 197, 94), rgb(74, 222, 128));
    }
    .stock-progress-bar.medium {
        background: linear-gradient(90deg, rgb(234, 179, 8), rgb(250, 204, 21));
    }
    .stock-progress-bar.low {
        background: linear-gradient(90deg, rgb(239, 68, 68), rgb(248, 113, 113));
    }
    
    /* Filter Tags */
    .filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.625rem;
        background-color: rgba(59, 130, 246, 0.1);
        color: rgb(59, 130, 246);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .filter-tag .remove {
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .filter-tag .remove:hover {
        opacity: 1;
    }
</style>
@endpush

@section('scripts')
<script>
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

    // Toggle variation panel
    function toggleVariationPanel(rowId) {
        const row = document.querySelector('[data-row-id="' + rowId + '"]');
        const panelRow = document.getElementById('variation-row-' + rowId);
        const panel = document.getElementById('variation-panel-' + rowId);
        
        if (!row || !panelRow || !panel) return;
        
        const isExpanded = row.classList.contains('expanded');
        
        // Close all other expanded rows
        document.querySelectorAll('.stock-row.expanded').forEach(expandedRow => {
            if (expandedRow !== row) {
                const otherId = expandedRow.getAttribute('data-row-id');
                const otherPanelRow = document.getElementById('variation-row-' + otherId);
                const otherPanel = document.getElementById('variation-panel-' + otherId);
                expandedRow.classList.remove('expanded');
                if (otherPanelRow) otherPanelRow.classList.remove('expanded');
                if (otherPanel) otherPanel.classList.remove('show');
            }
        });
        
        if (isExpanded) {
            row.classList.remove('expanded');
            panelRow.classList.remove('expanded');
            panel.classList.remove('show');
        } else {
            row.classList.add('expanded');
            panelRow.classList.add('expanded');
            panel.classList.add('show');
        }
    }

    function removeFilter(filterKey) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.delete(filterKey);
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.location.href = newUrl;
    }

    function clearAllFilters() {
        window.location.href = window.location.pathname;
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        setupDebouncedSearch();
        
        // Set up expand/collapse on stock rows
        document.querySelectorAll('.stock-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('a') || e.target.closest('button')) return;
                const rowId = this.getAttribute('data-row-id');
                toggleVariationPanel(rowId);
            });
        });
    });
</script>
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Reports') }}
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
            <p class="text-muted">{{ translate('Stock Reports') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    @php
        $totalProducts = $stockData->total();
        $totalStock = $stockData->sum('total_stock');
        $totalValue = $stockData->sum('total_stock_value');
        $lowStockCount = $stockData->filter(function($product) {
            return ($product->total_stock ?? 0) <= $product->alert_qty;
        })->count();
    @endphp

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="text-sm text-muted">{{ translate('Total Products') }}</div>
                <div class="text-xl font-bold">{{ number_format($totalProducts) }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div>
                <div class="text-sm text-muted">{{ translate('Total Stock') }}</div>
                <div class="text-xl font-bold">{{ number_format($totalStock) }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div>
                <div class="text-sm text-muted">{{ translate('Total Value') }}</div>
                <div class="text-xl font-bold">{{ formatPrice($totalValue) }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="text-sm text-muted">{{ translate('Low Stock Items') }}</div>
                <div class="text-xl font-bold">{{ number_format($lowStockCount) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter">

                <form action="" method="GET">
                    <div class="w-full">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 pb-2">
                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="categoryId" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Categories') }}" value=""
                                        selected="{{ Request::get('categoryId') }}" />
                                    @foreach ($categories as $cat)
                                        <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name') }}"
                                            value="{{ $cat->id }}"
                                            selected="{{ Request::get('categoryId') == $cat->id ? $cat->id : 0 }}" />

                                        @foreach ($cat->childrenCategories as $childCategory)
                                            @include('backend.admin.categories.subCategory', [
                                                'subCategory' => $childCategory,
                                                'langKey' => \Session::get('locale'),
                                                'category' => null,
                                            ])
                                        @endforeach
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="brandId" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Brands') }}" value=""
                                        selected="{{ Request::get('brandId') }}" />
                                    @foreach ($brands as $brand)
                                        <x-backend.inputs.select-option name="{{ $brand->collectTranslation('name') }}"
                                            value="{{ $brand->id }}"
                                            selected="{{ Request::get('brandId') == $brand->id ? $brand->id : 0 }}" />
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="warehouseId" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('All Warehouses') }}" value=""
                                        selected="{{ Request::get('warehouseId') }}" />
                                    @foreach ($warehouses as $warehouse)
                                        <x-backend.inputs.select-option name="{{ $warehouse->name }}"
                                            value="{{ $warehouse->id }}"
                                            selected="{{ Request::get('warehouseId') == $warehouse->id ? $warehouse->id : 0 }}" />
                                    @endforeach
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="sort" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Sort By') }}" value=""
                                        selected="{{ Request::get('sort') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Product Name') }}" value="product_name"
                                        selected="{{ Request::get('sort') == 'product_name' ? 'product_name' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Stock Amount') }}" value="stock_amount"
                                        selected="{{ Request::get('sort') == 'stock_amount' ? 'stock_amount' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Stock Value') }}" value="stock_value"
                                        selected="{{ Request::get('sort') == 'stock_value' ? 'stock_value' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-2">
                                <x-backend.inputs.select name="sort_dir" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Ascending') }}" value="asc"
                                        selected="{{ Request::get('sort_dir', 'asc') == 'asc' ? 'asc' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Descending') }}" value="desc"
                                        selected="{{ Request::get('sort_dir') == 'desc' ? 'desc' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-2 xl:col-span-2">
                                <x-backend.inputs.search-input name="search" placeholder="{{ translate('Search products...') }}" value="{{ $searchKey }}"
                                    class="search-input w-full" />
                            </div>

                            <div class="sm:col-span-2 xl:col-span-12 flex flex-col sm:flex-row gap-3 xl:justify-end">
                                <button type="submit"
                                        class="flex items-center justify-center gap-2 px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary/90 transition-colors w-full sm:w-auto xl:w-[180px] min-h-[42px]">
                                    <span class="text-sm">
                                        <i class="fas fa-filter"></i>
                                    </span>
                                    <span>{{ translate('Filter') }}</span>
                                </button>

                                @can('view_stock_report')
                                    <button id="exportPdfBtn"
                                            type="button"
                                            class="flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/80 transition-colors w-full sm:w-auto xl:w-[180px] min-h-[42px]">
                                        <span class="text-sm">
                                            <i class="fas fa-file-pdf"></i>
                                        </span>
                                        <span>{{ translate('Export PDF') }}</span>
                                    </button>
                                @endcan
                            </div>
                        </div>

                        @php
                            $activeFilters = [];
                            if (Request::get('categoryId')) {
                                $category = $categories->flatMap(function($cat) {
                                    return [$cat, ...$cat->childrenCategories];
                                })->firstWhere('id', Request::get('categoryId'));
                                if ($category) $activeFilters[] = ['type' => 'category', 'label' => $category->collectTranslation('name'), 'key' => 'categoryId'];
                            }
                            if (Request::get('brandId')) {
                                $brand = $brands->firstWhere('id', Request::get('brandId'));
                                if ($brand) $activeFilters[] = ['type' => 'brand', 'label' => $brand->collectTranslation('name'), 'key' => 'brandId'];
                            }
                            if (Request::get('warehouseId')) {
                                $warehouse = $warehouses->firstWhere('id', Request::get('warehouseId'));
                                if ($warehouse) $activeFilters[] = ['type' => 'warehouse', 'label' => $warehouse->name, 'key' => 'warehouseId'];
                            }
                            if (Request::get('search')) {
                                $activeFilters[] = ['type' => 'search', 'label' => '"' . Request::get('search') . '"', 'key' => 'search'];
                            }
                        @endphp

                        @if(count($activeFilters) > 0)
                            <div class="filter-tags">
                                <span class="text-sm text-muted mr-2">{{ translate('Active Filters:') }}</span>
                                @foreach($activeFilters as $filter)
                                    <span class="filter-tag">
                                        {{ $filter['label'] }}
                                        <span class="remove" onclick="removeFilter('{{ $filter['key'] }}')">&times;</span>
                                    </span>
                                @endforeach
                                <span class="filter-tag" style="background-color: rgba(239, 68, 68, 0.1); color: rgb(220, 38, 38); cursor: pointer;" onclick="clearAllFilters()">
                                    {{ translate('Clear All') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <div class="px-4 md:px-6 xl:px-8 pb-4">
                <table class="product-list-table w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th class="w-10"></th>
                            <th>{{ translate('Product Image') }}</th>
                            <th>{{ translate('Product Name') }}</th>
                            <th class="min-w-[150px]">{{ translate('Stock Amount') }}</th>
                            <th>{{ translate('Stock Value') }}</th>
                            <th class="w-24">{{ translate('Status') }}</th>
                        </tr>
                </thead>
                <tbody>
                    @forelse ($stockData as $product)
                        @php
                            $stockLevel = ($product->total_stock ?? 0) <= $product->alert_qty ? 'low' : 
                                         (($product->total_stock ?? 0) <= $product->alert_qty * 2 ? 'medium' : 'high');
                            $stockPercentage = $product->alert_qty > 0 
                                ? min((($product->total_stock ?? 0) / ($product->alert_qty * 2)) * 100, 100) 
                                : 100;
                        @endphp
                        <tr class="stock-row" data-row-id="{{ $product->id }}">
                            <td class="text-center">
                                <i class="fa-solid fa-chevron-down expand-icon text-muted"></i>
                            </td>
                            <td>
                                <div class="max-xs:hidden">
                                    <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                        class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md object-cover"
                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                </div>
                            </td>
                            <td>
                                <div class="max-w-[350px]">
                                    <div class="font-medium line-clamp-2">{{ $product->collectTranslation('name') }}</div>
                                    <div class="text-xs text-muted mt-1">{{ count($product->variation_stocks ?? []) }} {{ translate('variations') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <div class="inline-flex items-center gap-2">
                                        <span class="font-semibold text-lg">{{ $product->total_stock ?? 0 }}</span>
                                        @if ($stockLevel === 'low')
                                            <span class="stock-indicator stock-low">
                                                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                {{ translate('Low') }}
                                            </span>
                                        @elseif ($stockLevel === 'medium')
                                            <span class="stock-indicator stock-medium">
                                                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                                                {{ translate('Medium') }}
                                            </span>
                                        @else
                                            <span class="stock-indicator stock-high">
                                                <i class="fa-solid fa-check-circle text-xs"></i>
                                                {{ translate('Good') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="stock-progress">
                                        <div class="stock-progress-bar {{ $stockLevel }}" style="width: {{ $stockPercentage }}%"></div>
                                    </div>
                                    <div class="text-xs text-muted">{{ translate('Alert at') }}: {{ $product->alert_qty }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-lg">{{ formatPrice($product->total_stock_value ?? 0) }}</div>
                            </td>
                            <td>
                                @if(count($product->variation_stocks ?? []) > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 cursor-pointer hover:bg-blue-200 transition-colors">
                                        <i class="fa-solid fa-layer-group mr-1"></i>
                                        {{ translate('View Details') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ translate('No Variations') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        
                        <tr class="variation-panel-row" id="variation-row-{{ $product->id }}" style="width: 100%;">
                            <td colspan="6" style="border: none; padding: 0; width: 100%;">
                                <div id="variation-panel-{{ $product->id }}" class="variation-panel">
                                    <div class="border-t border-gray-200">
                                        <div class="p-4 bg-gray-50">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="font-semibold text-lg flex items-center gap-2">
                                                    <i class="fa-solid fa-box-open text-primary"></i>
                                                    {{ translate('Variation Stock Details') }}
                                                </h4>
                                                <div class="text-sm text-muted">{{ translate('Click row to collapse') }}</div>
                                            </div>
                                            
                                            @if(!empty($product->variation_stocks) && count($product->variation_stocks) > 0)
                                                <div class="variation-grid">
                                                    @foreach($product->variation_stocks as $variationName => $stockQty)
                                                        @php
                                                            $varStockLevel = $stockQty <= $product->alert_qty ? 'low' : 
                                                                             ($stockQty <= $product->alert_qty * 2 ? 'medium' : 'high');
                                                            $varStockPercentage = $product->alert_qty > 0 
                                                                ? min(($stockQty / ($product->alert_qty * 2)) * 100, 100) 
                                                                : 100;
                                                        @endphp
                                                        
                                                        <div class="variation-card">
                                                            <div class="flex items-start justify-between">
                                                                <div class="font-medium text-sm text-gray-900 line-clamp-2 flex-1">{{ $variationName }}</div>
                                                                @if($varStockLevel === 'low')
                                                                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-sm"></i>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="mt-2">
                                                                <div class="flex items-baseline gap-1">
                                                                    <span class="text-2xl font-bold {{ $varStockLevel === 'low' ? 'text-red-600' : ($varStockLevel === 'medium' ? 'text-yellow-600' : 'text-green-600') }}">
                                                                        {{ $stockQty }}
                                                                    </span>
                                                                    <span class="text-xs text-muted">{{ translate('units') }}</span>
                                                                </div>
                                                                
                                                                <div class="stock-progress mt-2">
                                                                    <div class="stock-progress-bar {{ $varStockLevel }}" style="width: {{ $varStockPercentage }}%"></div>
                                                                </div>
                                                                
                                                                <div class="flex items-center justify-between mt-2">
                                                                    <span class="text-xs text-muted">{{ translate('Alert') }}: {{ $product->alert_qty }}</span>
                                                                    <span class="stock-indicator stock-{{ $varStockLevel }} text-xs">
                                                                        @if($varStockLevel === 'low')
                                                                            {{ translate('Critical') }}
                                                                        @elseif($varStockLevel === 'medium')
                                                                            {{ translate('Warning') }}
                                                                        @else
                                                                            {{ translate('Healthy') }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-8 text-muted">
                                                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                                                    <p>{{ translate('No variations available for this product') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted">
                                <i class="fa-solid fa-inbox text-4xl mb-3 opacity-50"></i>
                                <p>{{ translate('No stock data found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <div class="card__footer">
                {{ $stockData->links() }}
            </div>
        </div>
    </div>


    <!-- Stock Report PDF Export JavaScript -->
    <script type="text/javascript">
        "use strict";
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.getElementById('exportPdfBtn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Get the form
                    const form = document.querySelector('form[method="GET"]');
                    if (!form) return;

                    // Create export form
                    const exportForm = document.createElement('form');
                    exportForm.method = 'POST';
                    exportForm.action = '{{ route("admin.stockReports.export") }}';

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

                    // Add form to document and submit
                    document.body.appendChild(exportForm);
                    exportForm.submit();

                    // Remove form after submission
                    setTimeout(() => {
                        document.body.removeChild(exportForm);
                    }, 100);
                });
            }
        });
    </script>

@endsection
