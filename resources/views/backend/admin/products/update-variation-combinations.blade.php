<div class="mt-8">
    <div class="flex items-center justify-between gap-3 relative">
        <h4 class="font-medium bg-background pr-4 relative z-[1]">{{ translate('Variations') }}</h4>
        <span
            class="absolute inline-block top-1/2 left-0 right-0 -translate-y-1/2 border-b border-[#E7E7E7] w-full h-0.5"></span>
    </div>

    <div class="product-variations init-accordion accordion-container border-collapse mt-5">
        @php
            $defaultWarehouse = shop()->warehouses()->where('is_default', 1)->first();
        @endphp
        @foreach ($combinations as $key => $combination)
            @php
                $name = '';
                $code_array = array_filter(explode('/', $combination->code));
                $lstKey = array_key_last($code_array);

                foreach ($code_array as $key2 => $comb) {
                    $comb = explode(':', $comb);

                    $option_name = \App\Models\Variation::find($comb[0])?->collectTranslation('name') ?? '';
                    $choice_name = \App\Models\VariationValue::find($comb[1])?->collectTranslation('name') ?? '';

                    $name .= $choice_name;

                    if ($lstKey != $key2) {
                        $name .= '-';
                    }
                }
            @endphp

            <div class="ac">
                <h2 class="ac-header">
                    <button type="button" class="ac-trigger">
                        <span class="text-muted mr-3">{{ translate('Variation') }} {{ $key + 1 }}:</span>
                        {{ $name }}
                    </button>
                </h2>
                <div class="ac-panel">
                    <div class="py-4 xl:py-7 px-3 md:px-6 xl:px-10 grid sm:grid-cols-2 xl:grid-cols-4 gap-7">
                        <input type="hidden" value="{{ $combination->code }}"
                            name="variations[{{ $key }}][code]">

                        <div>
                            <label class="mb-2">{{ translate('SKU') }}</label>
                            <x-backend.inputs.text name="variations[{{ $key }}][sku]"
                                value="{{ $combination->sku }}" placeholder="red-xl-346890" />
                        </div>
                        <div>
                            <label class="mb-2">{{ translate('Price') }}</label>
                            <x-backend.inputs.text name="variations[{{ $key }}][price]" min="0"
                                step="0.001" value="{{ $combination->price }}" placeholder="" />
                        </div>
                        <div>
                            <label class="mb-2">{{ translate('Discount') }}</label>
                            <x-backend.inputs.text type="number"
                                name="variations[{{ $key }}][discount_value]" min="0" step="0.001"
                                value="{{ $combination->discount_value }}" placeholder="" />
                        </div>
                        <div>
                            <label class="mb-2">{{ translate('Discount Type') }}</label>
                            <select class="theme-input h-auto p-3 py-2.5"
                                name="variations[{{ $key }}][discount_type]">
                                <option value="flat" @selected($combination->discount_type == 'flat')>{{ translate('Flat') }}</option>
                                <option value="percentage" @selected($combination->discount_type == 'percentage')>{{ translate('Percentage') }}
                                </option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 {{ !useInventory() ? 'xl:col-span-3' : 'xl:col-span-4' }} ">
                            <label class="mb-2">{{ translate('Image') }}</label>
                            <x-backend.inputs.file name="variations[{{ $key }}][image]"
                                value="{{ $combination->image }}" filesHint="" />
                        </div>

                        @php
                            $isColorVariation = false;
                            $code_array = array_filter(explode('/', $combination->code));
                            foreach ($code_array as $comb) {
                                $comb = explode(':', $comb);
                                $variation = \App\Models\Variation::find($comb[0]);
                                if ($variation && $variation->is_color == 1) {
                                    $isColorVariation = true;
                                    break;
                                }
                            }
                        @endphp
                        @if (\App\Models\FeatureFlag::isEnabled('color_image_enabled') && $isColorVariation)
                            <div class="sm:col-span-2 xl:col-span-4">
                                <label class="mb-2">{{ translate('Color Swatch Image') }}</label>
                                @php
                                    $combinationImage = \App\Models\ProductVariationCombination::where('product_id', $combination->product_id)
                                        ->where('product_variation_id', $combination->id)
                                        ->first();
                                    $hasExistingImage = !empty($combinationImage?->image);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <x-backend.inputs.file name="variations[{{ $key }}][combination_image]"
                                        value="{{ $combinationImage?->image }}" filesHint="" />
                                    <button type="button"
                                        class="use-variation-image-btn text-nowrap text-xs px-2 py-1.5 bg-secondary text-white rounded hover:bg-secondary/90 {{ $hasExistingImage ? 'hidden' : '' }}"
                                        data-variation-image-input="variations[{{ $key }}][image]"
                                        data-combination-image-input="variations[{{ $key }}][combination_image]">
                                        {{ translate('Use Image') }}
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ translate('Small image shown in color selector') }}</p>
                            </div>
                        @endif

                        @if (!useInventory())
                            <div>
                                <label class="mb-2">{{ translate('Stock') }}</label>
                                <x-backend.inputs.text type="number" name="variations[{{ $key }}][stock_qty]"
                                    min="0"
                                    value="{{ $defaultWarehouse ? $combination->productVariationStocks()->where('warehouse_id', $defaultWarehouse->id)->where('product_variation_id', $combination->id)->first()?->stock_qty ?? 0 : 0 }}"
                                    placeholder="" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
