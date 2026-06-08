<form
    @if (!Route::is(routePrefix() . '.purchase-return.create')) action="{{ $purchaseOrder ? route(routePrefix() . '.purchase-orders.update', $purchaseOrder->id) : route(routePrefix() . '.purchase-orders.store') }}"
    method="POST" 
    @else
    action="{{ route(routePrefix() . '.purchase-return.store', $purchaseOrder->id) }}"
    method="POST" @endif
    class="po-form">

    @csrf
    @if ($purchaseOrder && !Route::is(routePrefix() . '.purchase-return.create'))
        @method('PUT')
    @endif

    @php
        $returnOrder = null;
    @endphp

    @if (Route::is(routePrefix() . '.purchase-return.create'))
        @php
            $returnOrder = $purchaseOrder->return;
        @endphp
    @endif

    <div class="space-y-4">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-4">
                <x-backend.inputs.select label="Warehouse" :labelInline="false" name="warehouse_id" class=""
                    :isDisabled="$purchaseOrder != null">
                    @if (!$purchaseOrder)
                        <x-backend.inputs.select-option name="Select warehouse" value="" />
                        @foreach ($warehouses as $warehouse)
                            <x-backend.inputs.select-option name="{{ $warehouse?->name ?? translate('N/A') }}"
                                value="{{ $warehouse->id }}" />
                        @endforeach
                    @else
                        <x-backend.inputs.select-option
                            name="{{ $purchaseOrder->warehouse?->name ?? translate('N/A') }}"
                            value="{{ $purchaseOrder->warehouse_id }}" />
                    @endif
                </x-backend.inputs.select>
            </div>

            <div class="col-span-12 md:col-span-4">
                <x-backend.inputs.select label="Supplier" :labelInline="false" name="supplier_id" class=""
                    :isDisabled="Route::is(routePrefix() . '.purchase-return.create')">
                    @if (!Route::is(routePrefix() . '.purchase-return.create'))
                        <x-backend.inputs.select-option name="Select supplier" value="" />
                        @foreach ($suppliers as $supplier)
                            <x-backend.inputs.select-option name="{{ $supplier->name }}" value="{{ $supplier->id }}"
                                selected="{{ $purchaseOrder?->supplier_id }}" />
                        @endforeach
                    @else
                        <x-backend.inputs.select-option name="{{ $purchaseOrder->supplier?->name ?? translate('N/A') }}"
                            value="{{ $purchaseOrder->supplier_id }}" />
                    @endif
                </x-backend.inputs.select>
            </div>

            <div class="col-span-12 md:col-span-4">
                <x-backend.inputs.datepicker label="Date" :labelInline="false" name="date" placeholder="Pick a date"
                    value="{{ !Route::is(routePrefix() . '.purchase-return.create') ? ($purchaseOrder ? date('d-m-y', $purchaseOrder->date) : '') : ($returnOrder ? date('d-m-y', $returnOrder->date) : '') }}" />
            </div>
        </div>

        @if (!Route::is(routePrefix() . '.purchase-return.create'))
            <div class="group relative !mt-4" tabindex="0">
                <x-backend.inputs.inventory-search-input label="Product" class="purchase-order-product-search-input"
                    name="" placeholder="Search product by name" value="" />

                <div
                    class="po-product-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                    <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                        <span class="uppercase">{{ translate('Search Results') }}</span>
                    </div>

                    {{-- search result here --}}
                    <div class="divide-y divide-[#78787829] po-search-results">
                        @include(
                            'backend.' . routePrefix() . '.inventory.purchase-orders.product-search-results',
                            [
                                'productVariations' => [],
                            ]
                        )
                    </div>
                </div>
            </div>
        @endif


        {{-- start:order list --}}
        <div class="!mt-4">
            <label class="theme-input-label !pt-0 input-required">
                {{ translate('Order Items') }}
            </label>
            @php
                $orders = collect();

                if ($purchaseOrder) {
                    $orders = $purchaseOrder->orders;
                }

                if (Route::is(routePrefix() . '.purchase-return.create') && $returnOrder) {
                    $orders = $returnOrder->orders;
                }
            @endphp

            @include('backend.' . routePrefix() . '.inventory.purchase-orders.new-order-table', [
                'orders' => $orders,
                'returnOrder' => $returnOrder,
            ])
        </div>
        {{-- end:order list --}}

        {{-- start:summary --}}
        <div class="!my-6 flex justify-end">
            <table class="w-[350px] my-4">
                <tbody>
                    <tr
                        class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background">

                        <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                            {{ translate('Tax') }} (<span
                                class="tax-summary-percentage">{{ !Route::is(routePrefix() . '.purchase-return.create') ? number_format($purchaseOrder?->tax_percentage ?? 0, 3) : number_format($returnOrder?->tax_percentage ?? 0, 3) }}</span>%)
                        </td>
                        <td
                            class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap tax-summary">
                            {{ !Route::is(routePrefix() . '.purchase-return.create') ? number_format($purchaseOrder?->tax_value ?? 0, 3) : number_format($returnOrder?->tax_value ?? 0, 3) }}
                        </td>
                    </tr>
                    <tr
                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">

                        <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                            {{ translate('Discount') }}
                        </td>
                        <td
                            class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap discount-summary">
                            {{ !Route::is(routePrefix() . '.purchase-return.create') ? number_format($purchaseOrder?->discount ?? 0, 3) : number_format($returnOrder?->discount ?? 0, 3) }}
                        </td>

                    </tr>
                    <tr
                        class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background">

                        <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                            {{ translate('Shipping') }}
                        </td>
                        <td
                            class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap shipping-summary">
                            {{ !Route::is(routePrefix() . '.purchase-return.create') ? number_format($purchaseOrder?->shipping ?? 0, 3) : number_format(0, 3) }}
                        </td>

                    </tr>
                    <tr
                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                        <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                            {{ translate('Grand Total') }}
                        </td>
                        <td
                            class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap grand-total-summary">
                            {{ !Route::is(routePrefix() . '.purchase-return.create') ? number_format($purchaseOrder?->grand_total ?? 0, 3) : number_format(0, 3) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- end:summary --}}


        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 md:col-span-3">
                <x-backend.inputs.select label="Status" :labelInline="false" name="status" class=""
                    data-search="false">
                    @if (!Route::is(routePrefix() . '.purchase-return.create'))
                        <x-backend.inputs.select-option name="Pending" value="pending"
                            selected="{{ $purchaseOrder?->status }}" />
                        <x-backend.inputs.select-option name="Ordered" value="ordered"
                            selected="{{ $purchaseOrder?->status }}" />
                        <x-backend.inputs.select-option name="Received" value="received"
                            selected="{{ $purchaseOrder?->status }}" />
                    @else
                        <x-backend.inputs.select-option name="Pending" value="pending"
                            selected="{{ $returnOrder?->status }}" />
                        <x-backend.inputs.select-option name="Completed" value="completed"
                            selected="{{ $returnOrder?->status }}" />
                    @endif
                </x-backend.inputs.select>
            </div>

            <div class="col-span-12 md:col-span-3">
                <x-backend.inputs.number label="Tax(%)" onkeyup="calculateSummary()" :labelInline="false" name="tax"
                    placeholder="Type tax percentage"
                    value="{{ !Route::is(routePrefix() . '.purchase-return.create') ? $purchaseOrder?->tax_percentage ?? 0 : ($returnOrder ? $returnOrder->tax_percentage : 0) }}"
                    min="0" max="100" step="0.001" />
            </div>

            <div class="col-span-12 md:col-span-3">
                <x-backend.inputs.number label="Discount" onkeyup="calculateSummary()" :labelInline="false" name="discount"
                    placeholder="Type discount amount"
                    value="{{ !Route::is(routePrefix() . '.purchase-return.create') ? $purchaseOrder?->discount ?? 0 : ($returnOrder ? $returnOrder->discount : 0) }}"
                    min="0" step="0.001" />
            </div>
            <div class="col-span-12 md:col-span-3">
                <x-backend.inputs.number label="Shipping" onkeyup="calculateSummary()" :labelInline="false" name="shipping"
                    placeholder="Type shipping amount"
                    value="{{ !Route::is(routePrefix() . '.purchase-return.create') ? $purchaseOrder?->shipping ?? 0 : ($returnOrder ? $returnOrder->shipping : 0) }}"
                    min="0" step="0.001" />
            </div>
        </div>

        <x-backend.inputs.textarea label="Note" :labelInline="false" name="note" placeholder="Type few words..."
            value="{{ !Route::is(routePrefix() . '.purchase-return.create') ? $purchaseOrder?->note : $returnOrder?->note }}"
            :isRequired="false" />

        <div class="flex justify-end">
            <x-backend.inputs.button
                buttonText="{{ !Route::is(routePrefix() . '.purchase-return.create') ? 'Save Purchase Order' : 'Return Purchase Order' }}"
                type="submit" />
        </div>
    </div>
</form>
