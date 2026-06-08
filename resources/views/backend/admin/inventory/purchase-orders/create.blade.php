@extends('layouts.admin')

@section('title')
    {{ translate('Add New Purchase Order') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Purchase Order') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.purchase-orders.index') }}" class="font-bold ">{{ translate('Purchase Orders') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Purchase Order') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    @php
        $warehousesData = [];
        foreach ($warehouses as $warehouse) {
            $warehousesData[] = ['id' => $warehouse->id, 'name' => $warehouse->name];
        }
        
        $suppliersData = [];
        foreach ($suppliers as $supplier) {
            $suppliersData[] = ['id' => $supplier->id, 'name' => $supplier->name];
        }
        
        $initialFormData = [
            'warehouseId' => null,
            'supplierId' => null,
            'date' => date('Y-m-d'),
            'status' => 'pending',
            'tax' => 0,
            'discount' => 0,
            'shipping' => 0,
            'note' => '',
        ];
    @endphp

    <div id="purchase-order-root"></div>

    @section('scripts')
        <script>
            window.purchaseOrderData = {
                warehouses: {!! json_encode($warehousesData) !!},
                suppliers: {!! json_encode($suppliersData) !!},
                existingOrderItems: [],
                initialFormData: {!! json_encode($initialFormData) !!},
                useInventory: {{ useInventory() ? 'true' : 'false' }},
                isEdit: false,
                isReturn: false,
                submitUrl: '{{ route('admin.purchase-orders.store') }}',
                method: 'POST',
            };
        </script>
        
        @viteReactRefresh
        @vite(['resources/backend/purchase-order/index.tsx'])
    @endsection
@endsection
