@if (count($productVariations) > 0)
    @foreach ($productVariations as $key => $variation)
        @php
            $product = $variation->product;
            $name = '';
            $code_array = array_filter(explode('/', $variation->code));
            $lstKey = array_key_last($code_array);

            foreach ($code_array as $key2 => $comb) {
                $comb = explode(':', $comb);

                $option_name = \App\Models\Variation::find($comb[0])->collectTranslation('name');
                $choice_name = \App\Models\VariationValue::find($comb[1])->collectTranslation('name');

                $name .= $choice_name;

                if ($lstKey != $key2) {
                    $name .= '-';
                }
            }

            $stock = $variation->productVariationStocks()->where('warehouse_id', $warehouseId)->first();

            $stockQty = 0;
            if (!is_null($stock)) {
                $stockQty = $stock->stock_qty;
            }

            $tax = variationPrice($product, $variation) - variationPrice($product, $variation, false);
            $discount =
                variationPrice($product, $variation, false) - variationDiscountedPrice($product, $variation, false);
            $subtotal = $variation->price + $tax - $discount;
            $selectedVariationIds = $selectedVariationIds ?? [];
        @endphp

        <a class="cursor-pointer stock-product block py-4 px-6 hover:bg-primary/10" onclick="handlePoProductClick(this)"
            data-variation-id="{{ $variation->id }}"
            data-name="{{ $product->collectTranslation('name') . ' - ' . $name }}" data-stock="{{ $stockQty }}"
            data-unit="{{ $product->unit->collectTranslation('name') }}" data-discount="{{ $discount }}"
            data-tax="{{ $tax }}" data-subtotal="{{ $subtotal }}" data-price="{{ $variation->price }}">
            {{ $product->collectTranslation('name') }} @if ($product->has_variation)
                - <span class="text-orange-500 font-bold">{{ $name }}</span>
            @endif
        </a>
    @endforeach

    @if ($productVariations->hasMorePages())
        <div class="text-center p-5">
            <button type="button" class="btn button--warning rounded p-2"
                onclick="loadMorePoProducts(this, '{{ $productVariations->nextPageUrl() }}')">
                <div class="flex items-center">
                    <!-- Inner Ring -->
                    <div
                        class="w-[14px] h-[14px] rounded-full animate-spin
                border-2 border-solid border-white border-t-transparent hidden load-more-spin">
                    </div>
                    <div class="ms-2">{{ translate('Load more..') }}</div>
                </div>
            </button>
        </div>
    @endif
@else
    {{-- no results --}}
    <div class="flex justify-center items-center py-[35px]">
        {{ translate('No results') }}
    </div>
@endif
