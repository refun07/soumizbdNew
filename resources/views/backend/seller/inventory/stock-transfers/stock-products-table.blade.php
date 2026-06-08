<div class="stocks">
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
            <div class="inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-primary/10 border-b border-border">
                            <tr class="text-left">
                                <th class="p-4 ps-6 w-[60px]">
                                    #
                                </th>
                                <th class="p-4 {{ !Route::is('seller.stockTransfers.show') ? 'w-[200px]' : 'w-[280px]' }}"
                                    data-breakpoints="xs sm">
                                    {{ translate('Product') }}
                                </th>
                                @if (!Route::is('seller.stockTransfers.show'))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Current Stock') }}
                                    </th>
                                @endif

                                <th class="p-4 {{ !Route::is('seller.stockTransfers.show') ? '' : 'text-end' }}"
                                    data-breakpoints="xs sm">
                                    {{ translate('Qty') }}
                                </th>

                                @if (!Route::is('seller.stockTransfers.show'))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Action') }}
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="stocks-tbody">
                            @if (count($stockProductVariations) > 0)
                                @foreach ($stockProductVariations as $key => $stockProductVariation)
                                    @php
                                        $product = $stockProductVariation->product;
                                        $productVariation = $stockProductVariation->productVariation;
                                    @endphp

                                    <input type="hidden" name="purchaseOrderProductVariationIds[]"
                                        value="{{ $stockProductVariation->id }}">

                                    <tr
                                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            <span class="tr-length">{{ $key + 1 }}</span>
                                            <input type="hidden" name="selectedVariationIds[]"
                                                value="{{ $stockProductVariation->product_variation_id }}">
                                        </td>

                                        <td class="text-sm text-foreground font-light px-6 py-4 w-[200px] tr-name">
                                            {{ $stockProductVariation->product->collectTranslation('name') }}
                                            @if ($stockProductVariation->product->has_variation)
                                                -
                                                {{ generateVariationName($stockProductVariation->productVariation->code) }}
                                            @endif
                                        </td>

                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock text-end">
                                            {{ $stockProductVariation->qty }} <span
                                                class="tr-unit">{{ $product->unit?->collectTranslation('name') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="bg-background no-data">
                                    <td colspan="9"
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                        {{ translate('No data') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
