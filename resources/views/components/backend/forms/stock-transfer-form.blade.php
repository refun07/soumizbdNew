@if (!Route::is(routePrefix() . '.stockTransfers.show'))
    <form action="{{ route(routePrefix() . '.stockTransfers.store') }}" method="POST" class="stocks-form">
        @csrf
@endif

<div class="space-y-4">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
            <x-backend.inputs.select label="From Warehouse" :labelInline="false" name="from_warehouse_id" class=""
                isDisabled="{{ Route::is(routePrefix() . '.stockTransfers.show') ? true : false }}">
                <x-backend.inputs.select-option name="Select from warehouse" value="" />
                @foreach ($warehouses as $warehouse)
                    <x-backend.inputs.select-option name="{{ $warehouse->name }}" value="{{ $warehouse->id }}"
                        selected="{{ $stockTransfer?->from_warehouse_id }}" />
                @endforeach
            </x-backend.inputs.select>
        </div>

        <div class="col-span-12 md:col-span-6">
            <x-backend.inputs.select label="To Warehouse" :labelInline="false" name="to_warehouse_id" class=""
                isDisabled="{{ Route::is(routePrefix() . '.stockTransfers.show') ? true : false }}">
                <x-backend.inputs.select-option name="Select to warehouse" value="" />
                @foreach ($warehouses as $warehouse)
                    <x-backend.inputs.select-option name="{{ $warehouse->name }}" value="{{ $warehouse->id }}"
                        selected="{{ $stockTransfer?->to_warehouse_id }}" />
                @endforeach
            </x-backend.inputs.select>
        </div>
    </div>

    @if (!Route::is(routePrefix() . '.stockTransfers.show'))
        <div class="group relative !mt-4" tabindex="0">
            <x-backend.inputs.search-input label="Product" class="stocks-product-search-input" name=""
                placeholder="Search product by name" value="" />

            <div
                class="stock-product-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                    <span class="uppercase">{{ translate('Search Results') }}</span>
                </div>

                {{-- search result here --}}
                <div class="divide-y divide-[#78787829] stock-search-results">
                    @include(
                        'backend.' . routePrefix() . '.inventory.stock-transfers.product-search-results',
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
            {{ translate('Transferred Items') }}
        </label>
        @php
            $stockProductVariations = collect();
            if ($stockTransfer) {
                $stockProductVariations = $stockTransfer->productVariations;
            }
        @endphp

        @include('backend.' . routePrefix() . '.inventory.stock-transfers.stock-products-table', [
            'stockProductVariations' => $stockProductVariations,
        ])
    </div>
    {{-- end:order list --}}

    <x-backend.inputs.textarea label="Note" :labelInline="false" name="note" placeholder="Type few words..."
        value="{{ $stockTransfer?->note }}" :isRequired="false"
        isDisabled="{{ Route::is(routePrefix() . '.stockTransfers.show') ? true : false }}" />

    @if (!Route::is(routePrefix() . '.stockTransfers.show'))
        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Changes" type="submit" />
        </div>
    @endif
</div>

@if (!Route::is(routePrefix() . '.stockTransfers.show'))
    </form>
@endif
