@if (!Route::is(routePrefix() . '.stockAdjustments.show'))
    <form action="{{ route(routePrefix() . '.stockAdjustments.store') }}" method="POST" class="stocks-form">
        @csrf
@endif

 <div class="space-y-4">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
            <x-backend.inputs.select label="Warehouse" :labelInline="false" name="warehouse_id" class=""
                isDisabled="{{ Route::is(routePrefix() . '.stockAdjustments.show') ? true : false }}">
                <x-backend.inputs.select-option name="Select warehouse" value="" />
                @foreach ($warehouses as $warehouse)
                    <x-backend.inputs.select-option name="{{ $warehouse->name }}" value="{{ $warehouse->id }}"
                        selected="{{ $stockAdjustment?->warehouse_id }}" />
                @endforeach
            </x-backend.inputs.select>
        </div>

        <div class="col-span-12 md:col-span-6 supplier-field-wrapper" @if(!useInventory()) style="display:none;" @endif>
            <x-backend.inputs.select label="Supplier" :labelInline="false" name="supplier_id" class=""
                :isRequired="false"
                isDisabled="{{ Route::is(routePrefix() . '.stockAdjustments.show') ? true : false }}">
                <x-backend.inputs.select-option name="Select supplier" value="" />
                @foreach ($suppliers as $supplier)
                    <x-backend.inputs.select-option name="{{ $supplier->name }}" value="{{ $supplier->id }}"
                        selected="{{ $stockAdjustment?->supplier_id }}" />
                @endforeach
            </x-backend.inputs.select>
        </div>
    </div>

    @if (!Route::is(routePrefix() . '.stockAdjustments.show'))
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
                        'backend.' . routePrefix() . '.inventory.stock-adjustments.product-search-results',
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
            {{ translate('Adjustment Items') }}
        </label>
        @php
            $stockProductVariations = collect();
            if ($stockAdjustment) {
                $stockProductVariations = $stockAdjustment->productVariations;
            }
        @endphp

        @include('backend.' . routePrefix() . '.inventory.stock-adjustments.stock-products-table', [
            'stockProductVariations' => $stockProductVariations,
        ])
    </div>
    {{-- end:order list --}}

    <x-backend.inputs.textarea label="Note" :labelInline="false" name="note" placeholder="Type few words..."
        value="{{ $stockAdjustment?->note }}" :isRequired="false"
        isDisabled="{{ Route::is(routePrefix() . '.stockAdjustments.show') ? true : false }}" />

    @if (!Route::is(routePrefix() . '.stockAdjustments.show'))
        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Changes" type="submit" />
        </div>
    @endif
</div>

@if (!Route::is(routePrefix() . '.stockAdjustments.show'))
    </form>
@endif
