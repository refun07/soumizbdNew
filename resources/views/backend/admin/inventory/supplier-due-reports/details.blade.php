@extends('layouts.admin')

@section('title')
    {{ translate('Supplier Due Details') }} - {{ $supplier->name }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Supplier Due Details') }}
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
            <a href="{{ route('admin.supplierDueReports.index') }}" class="text-muted">{{ translate('Supplier Due Reports') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ $supplier->name }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- Supplier Information Card -->
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-store text-primary text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-foreground mb-2">{{ $supplier->name }}</h2>
                    <div class="text-sm text-muted space-y-1">
                        @if(!empty($supplier->email))
                            <div>
                                <i class="fa-regular fa-envelope mr-2"></i>
                                {{ $supplier->email }}
                            </div>
                        @endif
                        @if(!empty($supplier->phone_no))
                            <div>
                                <i class="fa-regular fa-phone mr-2"></i>
                                {{ $supplier->phone_no }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.supplierDueReports.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ translate('Back to Reports') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-file-lines text-blue-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ $purchaseOrders->count() }}</h3>
                    <p class="text-muted">{{ translate('Due Orders') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-dollar-sign text-green-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ formatPrice($purchaseOrders->sum('grand_total')) }}</h3>
                    <p class="text-muted">{{ translate('Total Order Amount') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-check-circle text-emerald-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ formatPrice($purchaseOrders->sum('paid')) }}</h3>
                    <p class="text-muted">{{ translate('Total Paid') }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-foreground">{{ formatPrice($purchaseOrders->sum('due')) }}</h3>
                    <p class="text-muted">{{ translate('Total Due') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table -->
    <div class="card">
        <div class="card__title">
            <h3 class="text-lg font-semibold">{{ translate('Due Purchase Orders') }}</h3>
        </div>

        <div class="theme-table">
            <table class="w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>{{ translate('Order ID') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Warehouse') }}</th>
                        <th>{{ translate('Total Amount') }}</th>
                        <th>{{ translate('Paid') }}</th>
                        <th>{{ translate('Due') }}</th>
                        <th>{{ translate('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $order)
                        <tr>
                            <td>
                                <div class="font-semibold text-foreground">
                                    #{{ str_pad($order->reference_code, 6, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-muted">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    {{ $order->warehouse?->name ?? translate('N/A') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-foreground">
                                    {{ formatPrice($order->grand_total) }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-green-600">
                                    {{ formatPrice($order->paid) }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-red-600">
                                    {{ formatPrice($order->due) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning">
                                    {{ translate('Due') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-muted">
                                {{ translate('No due purchase orders found for this supplier') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($purchaseOrders->isNotEmpty())
                <div class="card__footer border-t pt-4">
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-muted">
                            {{ translate('Total Orders') }}: {{ $purchaseOrders->count() }}
                        </div>
                        <div class="font-semibold">
                            {{ translate('Total Due Amount') }}: <span class="text-red-600">{{ formatPrice($purchaseOrders->sum('due')) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection