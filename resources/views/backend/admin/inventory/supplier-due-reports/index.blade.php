@extends('layouts.admin')

@section('title')
    {{ translate('Supplier Due Reports') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Supplier Due Reports') }}
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
            <p class="text-muted">{{ translate('Supplier Due Reports') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-users text-primary text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ $totalSuppliersWithDue ?? 0 }}</h3>
                    <p class="text-muted">{{ translate('Suppliers with Due') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-money-bill text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ formatPrice($totalDueAmount ?? 0) }}</h3>
                    <p class="text-muted">{{ translate('Total Due Amount') }}</p>
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
                            <div class="sm:col-span-1 xl:col-span-3">
                                <x-backend.inputs.select name="min_due" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Min Due Amount') }}" value=""
                                        selected="{{ ($filters['min_due'] ?? '') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Above 1,000') }}" value="1000"
                                        selected="{{ ($filters['min_due'] ?? '') == '1000' ? '1000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Above 5,000') }}" value="5000"
                                        selected="{{ ($filters['min_due'] ?? '') == '5000' ? '5000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Above 10,000') }}" value="10000"
                                        selected="{{ ($filters['min_due'] ?? '') == '10000' ? '10000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Above 25,000') }}" value="25000"
                                        selected="{{ ($filters['min_due'] ?? '') == '25000' ? '25000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Above 50,000') }}" value="50000"
                                        selected="{{ ($filters['min_due'] ?? '') == '50000' ? '50000' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-3">
                                <x-backend.inputs.select name="max_due" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Max Due Amount') }}" value=""
                                        selected="{{ ($filters['max_due'] ?? '') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Below 5,000') }}" value="5000"
                                        selected="{{ ($filters['max_due'] ?? '') == '5000' ? '5000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Below 10,000') }}" value="10000"
                                        selected="{{ ($filters['max_due'] ?? '') == '10000' ? '10000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Below 25,000') }}" value="25000"
                                        selected="{{ ($filters['max_due'] ?? '') == '25000' ? '25000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Below 50,000') }}" value="50000"
                                        selected="{{ ($filters['max_due'] ?? '') == '50000' ? '50000' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Below 100,000') }}" value="100000"
                                        selected="{{ ($filters['max_due'] ?? '') == '100000' ? '100000' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-3">
                                <x-backend.inputs.select name="sort" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Sort By') }}" value=""
                                        selected="{{ ($filters['sort'] ?? '') }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Supplier Name') }}" value="supplier_name"
                                        selected="{{ ($filters['sort'] ?? '') == 'supplier_name' ? 'supplier_name' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Total Due') }}" value="total_due"
                                        selected="{{ ($filters['sort'] ?? '') == 'total_due' ? 'total_due' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Total Orders') }}" value="total_orders"
                                        selected="{{ ($filters['sort'] ?? '') == 'total_orders' ? 'total_orders' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Last Purchase') }}" value="last_purchase_date"
                                        selected="{{ ($filters['sort'] ?? '') == 'last_purchase_date' ? 'last_purchase_date' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-1 xl:col-span-3">
                                <x-backend.inputs.select name="sort_dir" class="filterSelect w-full" :isRequired="false">
                                    <x-backend.inputs.select-option name="{{ translate('Ascending') }}" value="asc"
                                        selected="{{ ($filters['sort_dir'] ?? 'asc') == 'asc' ? 'asc' : '' }}" />
                                    <x-backend.inputs.select-option name="{{ translate('Descending') }}" value="desc"
                                        selected="{{ ($filters['sort_dir'] ?? '') == 'desc' ? 'desc' : '' }}" />
                                </x-backend.inputs.select>
                            </div>

                            <div class="sm:col-span-2 xl:col-span-8">
                                <x-backend.inputs.search-input name="search"
                                    placeholder="{{ translate('Search by supplier name, email, phone...') }}"
                                    value="{{ $searchKey }}"
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

                                @can('view_suppliers')
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
                    </div>
                </form>
            </div>


            <table class="supplier-due-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            {{ translate('Supplier Name') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Contact Info') }}
                        </th>
                        <th data-breakpoints="xs sm" class="min-w-[120px]">
                            {{ translate('Total Orders') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Total Order Amount') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Total Paid') }}
                        </th>
                        <th class="min-w-[120px]">
                            {{ translate('Total Due') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Last Purchase') }}
                        </th>
                        <th class="text-center">
                            {{ translate('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($supplierDueData as $supplier)
                        <tr>
                            <td>
                                <div class="max-w-[200px]">
                                    <div class="font-semibold text-foreground line-clamp-2">
                                        {{ $supplier->supplier_name }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    @if(!empty($supplier->supplier_email))
                                        <div class="text-muted">
                                            <i class="fa-regular fa-envelope mr-1"></i>
                                            {{ $supplier->supplier_email }}
                                        </div>
                                    @endif
                                    @if(!empty($supplier->supplier_phone))
                                        <div class="text-muted">
                                            <i class="fa-regular fa-phone mr-1"></i>
                                            {{ $supplier->supplier_phone }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="font-medium">{{ $supplier->total_orders }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-foreground">
                                    {{ formatPrice($supplier->total_order_amount ?? 0) }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-green-600">
                                    {{ formatPrice($supplier->total_paid ?? 0) }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-red-600">
                                    {{ formatPrice($supplier->total_due ?? 0) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-muted">
                                    @if($supplier->last_purchase_date)
                                        {{ \Carbon\Carbon::parse($supplier->last_purchase_date)->format('M d, Y') }}
                                    @else
                                        {{ translate('N/A') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <a href="{{ route('admin.supplierDueReports.details', $supplier->supplier_id) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ translate('View Details') }}">
                                        <i class="fa-regular fa-eye"></i>
                                        {{ translate('Details') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                {{ translate('No suppliers with due amounts found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="card__footer">
                {{ $supplierDueData->links() }}
            </div>
        </div>
    </div>


    <!-- Supplier Due Report PDF Export JavaScript -->
    <script type="text/javascript">
        "use strict";
        document.addEventListener('DOMContentLoaded', function() {
            const exportBtn = document.getElementById('exportPdfBtn');

            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Get current filters from the search form
                    const form = document.querySelector('form[method="GET"]');

                    // Create a new form for PDF export
                    const exportForm = document.createElement('form');
                    exportForm.method = 'POST';
                    exportForm.action = '{{ route("admin.supplierDueReports.export") }}';

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    exportForm.appendChild(csrfInput);

                    // Copy filter values from search form
                    if (form) {
                        const formData = new FormData(form);
                        for (const [key, value] of formData.entries()) {
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
