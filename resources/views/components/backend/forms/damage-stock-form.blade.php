@php $damageStock = $damageStock ?? null; @endphp
@if (!Route::is(routePrefix() . '.damageStocks.show'))
    <form action="{{ route(routePrefix() . '.damageStocks.store') }}" method="POST" class="damage-stocks-form">
        @csrf
@endif

<div class="space-y-4">
    <div class="grid grid-cols-12 gap-4">
        @if (useInventory())
            <div class="col-span-12 md:col-span-6">
                <x-backend.inputs.select label="Warehouse" :labelInline="false" name="warehouse_id"
                    isDisabled="{{ Route::is(routePrefix() . '.damageStocks.show') ? true : false }}">
                    <x-backend.inputs.select-option name="Select warehouse" value="" />
                    @foreach ($warehouses as $warehouse)
                        <x-backend.inputs.select-option name="{{ $warehouse->name }}" value="{{ $warehouse->id }}"
                            selected="{{ $damageStock?->warehouse_id }}" />
                    @endforeach
                </x-backend.inputs.select>
            </div>

            <div class="col-span-12 md:col-span-6">
                <x-backend.inputs.datepicker label="Damage Date" name="damage_date"
                    :labelInline="false"
                    value="{{ $damageStock?->return_date ?? date('d-m-Y') }}"
                    isDisabled="{{ Route::is(routePrefix() . '.damageStocks.show') ? true : false }}" />
            </div>
        @else
            <input type="hidden" name="warehouse_id" value="{{ $defaultWarehouseId }}">

            <div class="col-span-12 md:col-span-6">
                <x-backend.inputs.datepicker label="Damage Date" name="damage_date"
                    :labelInline="false"
                    value="{{ $damageStock?->return_date ?? date('d-m-Y') }}"
                    isDisabled="{{ Route::is(routePrefix() . '.damageStocks.show') ? true : false }}" />
            </div>
        @endif
    </div>

    @if (!Route::is(routePrefix() . '.damageStocks.show'))
        <div class="group relative !mt-4" tabindex="0">
            <x-backend.inputs.search-input label="Product" class="damage-product-search-input" name=""
                placeholder="Search product by name" value="" />

            <div
                class="damage-product-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                    <span class="uppercase">{{ translate('Search Results') }}</span>
                </div>

                <div class="divide-y divide-[#78787829] damage-search-results">
                    @include('backend.' . routePrefix() . '.inventory.damage-stocks.product-search-results', [
                        'productVariations' => [],
                    ])
                </div>
            </div>
        </div>
    @endif

    <div class="!mt-4">
        <label class="theme-input-label !pt-0 input-required">
            {{ translate('Damaged Items') }}
        </label>
        @include('backend.' . routePrefix() . '.inventory.damage-stocks.products-table')
    </div>

    @if (!Route::is(routePrefix() . '.damageStocks.show'))
        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Changes" type="submit" />
        </div>
    @endif
</div>

@if (!Route::is(routePrefix() . '.damageStocks.show'))
    </form>
@endif
