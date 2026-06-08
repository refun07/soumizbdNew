<div class="purchase-orders">
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
            <div class="inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-primary/10 border-b border-border">
                            <tr class="text-left">
                                <th class="p-4 ps-6">
                                    #
                                </th>
                                <th class="p-4 w-[200px]" data-breakpoints="xs sm">
                                    {{ translate('Product') }}
                                </th>
                                @if (!Route::is('admin.purchase-return.create'))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Default Unit Price') }}
                                    </th>
                                @endif

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Current Stock') }}
                                </th>

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Unit Price') }}
                                </th>

                                @if (Route::is('admin.purchase-return.create'))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Qty Purchased') }}
                                    </th>
                                @endif


                                <th class="p-4" data-breakpoints="xs sm">
                                    @if (!Route::is('admin.purchase-return.create'))
                                        {{ translate('Qty') }}
                                    @else
                                        {{ translate('Return Qty') }}
                                    @endif
                                </th>

                                @if (!Route::is('admin.purchase-return.create') && useInventory() && isset($purchaseOrder))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Expiry Date') }}
                                    </th>
                                @endif

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Discount') }}
                                </th>

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Tax') }}
                                </th>

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Subtotal') }}
                                </th>

                                @if (!Route::is('admin.purchase-return.create'))
                                    <th class="p-4" data-breakpoints="xs sm">
                                        {{ translate('Action') }}
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="purchase-orders-tbody">
                            @if (count($orders) > 0)
                                @foreach ($orders as $key => $order)
                                    @php
                                        $product = $order->product;
                                        $productVariation = $order->productVariation;

                                        $disabledInput = false;
                                        if ($product->trashed() || $productVariation->trashed()) {
                                            $disabledInput = true;
                                        }

                                        $stockQty = 0;
                                        $productVariationStock = $productVariation
                                            ->productVariationStocks()
                                            ->where('warehouse_id', $order->purchaseOrder->warehouse_id)
                                            ->first();

                                        if (!is_null($productVariationStock)) {
                                            $stockQty = $productVariationStock->stock_qty;
                                        }
                                        $max = null;
                                        if (Route::is('admin.purchase-return.create')) {
                                            $max = $order->qty;
                                        }
                                    @endphp

                                    <input type="hidden" name="purchaseOrderProductVariationIds[]"
                                        value="{{ $order->id }}">

                                    <tr
                                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                                        <td class="p-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            <span class="tr-length">{{ $key + 1 }}</span>
                                            <input type="hidden" name="selectedVariationIds[]"
                                                value="{{ $order->product_variation_id }}">
                                        </td>
                                        <td
                                            class="text-sm text-foreground font-light p-4 w-[200px] tr-name {{ $disabledInput ? 'text-red-500' : '' }}">
                                            {{ $product->collectTranslation('name') }} @if ($product->has_variation)
                                                - {{ generateVariationName($productVariation->code) }}
                                            @endif

                                            @if ($disabledInput)
                                                <span class="tooltip">
                                                    <span class="tooltip__toggler">
                                                        <i class="far fa-exclamation-circle"></i>
                                                    </span>
                                                    <span
                                                        class="tooltip__content">{{ translate('This item is not available in system now') }}</span>
                                                </span>
                                            @endif

                                        </td>

                                        @if (!Route::is('admin.purchase-return.create'))
                                            <td
                                                class="text-sm text-foreground font-light p-4 whitespace-nowrap tr-default-unit-price">
                                                {{ formatPrice($order->base_unit_price) }}
                                            </td>
                                        @endif

                                        <td
                                            class="p-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock">
                                            {{ $stockQty }} <span
                                                class="tr-unit">{{ $product->unit?->collectTranslation('name') }}</span>
                                        </td>
                                        <td
                                            class="text-sm text-foreground font-light p-4 whitespace-nowrap min-w-[150px]">
                                            @if (!Route::is('admin.purchase-return.create'))
                                                <x-backend.inputs.text labelInline="true" type="number"
                                                    name="unitPrice[]" min="0" onkeyup="calculateSubtotal(this)"
                                                    step="0.001" value="{{ $order->unit_price }}" />
                                            @else
                                                <input type="hidden" name="unitPrice[]"
                                                    value="{{ $order->unit_price }}">
                                                {{ formatPrice($order->unit_price) }}
                                            @endif
                                        </td>

                                        @if (Route::is('admin.purchase-return.create'))
                                            <td
                                                class="p-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock">
                                                {{ $order->qty }} <span
                                                    class="tr-unit">{{ $product->unit?->collectTranslation('name') }}</span>
                                            </td>
                                        @endif

                                        <td
                                            class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                                            @if (!Route::is('admin.purchase-return.create'))
                                                @if ($disabledInput)
                                                    <input type="hidden" name="stockQty[]"
                                                        value="{{ $order->qty }}">
                                                @endif
                                                <x-backend.inputs.number name="stockQty[]" min="1"
                                                    max="{{ $max }}" value="{{ $order->qty }}"
                                                    onkeyup="calculateSubtotal(this)" :isDisabled="$disabledInput" />
                                            @else
                                                @if ($disabledInput)
                                                    <input type="hidden" name="stockQty[]"
                                                        value="{{ $returnOrder ? $order->returned_qty : 0 }}">
                                                @endif
                                                <x-backend.inputs.number name="stockQty[]" min="0"
                                                    max="{{ $max }}"
                                                    value="{{ $returnOrder ? $order->returned_qty : 0 }}"
                                                    onkeyup="calculateSubtotal(this)" :isDisabled="$disabledInput" />
                                            @endif
                                        </td>

                                            @if (!Route::is('admin.purchase-return.create') && useInventory() && isset($purchaseOrder))
                                                <td class="text-sm text-foreground font-light p-4 whitespace-nowrap min-w-[150px]">
                                                    @php
                                                        // Only show expiry date if PO status is 'received'
                                                        $expiryDate = '';
                                                        if ($purchaseOrder && $purchaseOrder->status === 'received') {
                                                            // Get the stock entry for this purchase order item if it exists
                                                            $stockEntry = \App\Models\Stock::where('purchase_order_item_id', $order->id)->first();
                                                            $expiryDate = $stockEntry ? ($stockEntry->expiry_date ? date('Y-m-d', $stockEntry->expiry_date) : '') : '';
                                                        }
                                                    @endphp
                                                     <x-backend.inputs.datepicker labelInline="true"
                                                         type="single"
                                                         name="expiryDates[]"
                                                         value="{{ $expiryDate }}"
                                                         placeholder="{{ translate('Expiry Date') }}"
                                                         data-skip-datepicker-init />
                                                </td>
                                            @endif

                                        <td class="text-sm text-foreground font-light p-4 whitespace-nowrap">
                                            @if (!Route::is('admin.purchase-return.create'))
                                                <input type="hidden" name="discount[]"
                                                    value="{{ $order->discount / $order->qty }}">
                                                <input type="hidden" name="discountPrice[]"
                                                    value="{{ $order->discount }}">
                                                <span
                                                    class="tr-discount">{{ number_format($order->discount, 3) }}</span>
                                            @else
                                                <input type="hidden" name="discount[]"
                                                    value="{{ $returnOrder ? $order->discount / ($order->returned_qty > 0 ? $order->returned_qty : 1) : $order->discount / $order->qty }}">
                                                <input type="hidden" name="discountPrice[]"
                                                    value="{{ $returnOrder ? $order->discount : 0 }}">
                                                <span
                                                    class="tr-discount">{{ number_format($returnOrder ? $order->discount : 0, 3) }}</span>
                                            @endif
                                        </td>

                                        <td class="text-sm text-foreground font-light p-4 whitespace-nowrap">
                                            @if (!Route::is('admin.purchase-return.create'))
                                                <input type="hidden" name="tax[]"
                                                    value="{{ $order->tax / $order->qty }}">
                                                <input type="hidden" name="taxPrice[]" value="{{ $order->tax }}">
                                                <span class="tr-tax">{{ number_format($order->tax, 3) }}</span>
                                            @else
                                                <input type="hidden" name="tax[]"
                                                    value="{{ $returnOrder ? $order->tax / ($order->returned_qty > 0 ? $order->returned_qty : 1) : $order->tax / $order->qty }} }}">
                                                <input type="hidden" name="taxPrice[]"
                                                    value="{{ $returnOrder ? $order->tax : 0 }}">
                                                <span
                                                    class="tr-tax">{{ number_format($returnOrder ? $order->tax : 0, 3) }}</span>
                                            @endif
                                        </td>
                                        <td class="p-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            @if (!Route::is('admin.purchase-return.create'))
                                                <input type="hidden" name="subtotal[]"
                                                    value="{{ $order->grand_total }}">
                                                <span
                                                    class="tr-subtotal">{{ number_format($order->grand_total, 3) }}</span>
                                            @else
                                                <input type="hidden" name="subtotal[]"
                                                    value="{{ $returnOrder ? $order->grand_total : 0 }}">
                                                <span
                                                    class="tr-subtotal">{{ number_format($returnOrder ? $order->grand_total : 0, 3) }}</span>
                                            @endif
                                        </td>
                                        @if (!Route::is('admin.purchase-return.create'))
                                            <td class="p-4 whitespace-nowrap text-sm font-medium text-foreground">
                                                <button type="button"
                                                    class="text-md flex items-center justify-center"
                                                    onclick="removeOrderItem(this)">
                                                    <i class="fas fa-times text-red-500"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr class="bg-background no-data">
                                    <td colspan="{{ (!Route::is('admin.purchase-return.create') && useInventory() && isset($purchaseOrder)) ? 10 : 9 }}"
                                        class="p-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
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
