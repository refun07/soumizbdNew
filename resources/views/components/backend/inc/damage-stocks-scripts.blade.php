<script>
    "use strict";

    ATE.searchKey = '';

    $('.damage-stocks-form').on('submit', function(e) {
        let selectedVariationIds = $("input[name='product_variation_ids[]']")
            .map(function() {
                return $(this).val();
            }).get();

        if (selectedVariationIds.length < 1) {
            notifyMe('error', '{{ translate('Please select at least one product') }}');
            e.preventDefault();
        }
    });

    $('.damage-product-search-input').on('click', function() {
        debouncedProductsSearch();
        $(".damage-product-search").show();
    });

    function updateQtyValue($input, step) {
        const min = parseFloat($input.attr('min')) || 1;
        const currentValue = parseFloat($input.val()) || min;
        const nextValue = Math.max(min, currentValue + step);
        $input.val(nextValue);
    }

    window.adjustQty = function($this, step) {
        const $qtyInput = $($this).closest('.qty-control').find('input[name="quantities[]"]');
        updateQtyValue($qtyInput, step);
    };

    window.normalizeQty = function($this) {
        const $input = $($this);
        const min = parseFloat($input.attr('min')) || 1;
        const currentValue = parseFloat($input.val()) || min;
        $input.val(Math.max(min, currentValue));
    };

    const debouncedProductsSearch = debounce(getProducts, 300);
    $('.damage-product-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value;
        debouncedProductsSearch();
    }));

    function getProducts() {
        let warehouseId = $('[name=warehouse_id]').val();

        @if (!useInventory())
            if (!warehouseId && '{{ $defaultWarehouseId ?? "" }}') {
                warehouseId = '{{ $defaultWarehouseId }}';
            }
        @endif

        if (warehouseId == '') {
            notifyMe('warning', '{{ translate('Please select warehouse') }}');
            return;
        }

        let selectedVariationIds = $("input[name='product_variation_ids[]']")
            .map(function() {
                return $(this).val();
            }).get();

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.damageStocks.getProducts') }}',
            data: {
                searchKey: ATE.searchKey,
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $('.damage-search-results').html(data);
            }
        });
    }

    function loadMoreProducts($this, url) {
        $('.load-more-spin').removeClass('hidden');

        let selectedVariationIds = $("input[name='product_variation_ids[]']")
            .map(function() {
                return $(this).val();
            }).get();

        let warehouseId = $('[name=warehouse_id]').val();

        @if (!useInventory())
            if (!warehouseId && '{{ $defaultWarehouseId ?? "" }}') {
                warehouseId = '{{ $defaultWarehouseId }}';
            }
        @endif

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: url,
            data: {
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $($this).closest('div').remove();
                $(".damage-product-search-input input").focus();
                $('.damage-search-results').append(data);
            }
        });
    }

    function handleDamageProductClick($this) {
        let variationId = 0,
            length = 0,
            name = '',
            unit = '',
            stock = 0;

        $('.damage-tbody .no-data')?.remove();

        variationId = $($this).data('variation-id');
        length = $('.damage-tbody').children().length + 1;
        name = $($this).data('name');
        unit = $($this).data('unit');
        stock = $($this).data('stock');

        let tr = `<tr class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <span class="tr-length">${length}</span>
                            <input type="hidden" name="product_variation_ids[]" value="${variationId}">
                        </td>

                        <td class="text-sm text-foreground font-light px-6 py-4 w-[200px] tr-name">
                          ${name}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock">
                            ${stock} <span class="tr-unit">${unit}</span>
                        </td>

                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[170px]">
                            <div class="qty-control flex items-center gap-2">
                                <button type="button"
                                    class="h-[42px] w-[42px] rounded-md border border-border bg-background text-muted hover:bg-background-hover"
                                    onclick="adjustQty(this, -1)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" name="quantities[]" min="1" value="1"
                                    class="theme-input text-center" oninput="normalizeQty(this)" />
                                <button type="button"
                                    class="h-[42px] w-[42px] rounded-md border border-border bg-background text-muted hover:bg-background-hover"
                                    onclick="adjustQty(this, 1)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </td>

                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <select class="theme-input h-auto p-3" name="damage_types[]">
                                <option value="warehouse">{{ translate('Warehouse') }}</option>
                                <option value="transportation">{{ translate('Transportation') }}</option>
                                <option value="expired">{{ translate('Expired') }}</option>
                                <option value="manufacturing_defect">{{ translate('Manufacturing Defect') }}</option>
                            </select>
                        </td>

                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                            <input type="text" name="reasons[]" placeholder="{{ translate('Reason') }}"
                                class="theme-input" />
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeItem(this)">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;

        $('.damage-tbody').append(tr);
        $($this).remove();
        $(".damage-product-search").hide();
    }

    function removeItem($this) {
        $($this).closest('tr').remove();
    }

    $('[name=warehouse_id]').on('change', function() {
        let noSearch = `<div class="flex justify-center items-center py-[35px]">
                        {{ translate('No results') }}
                    </div>`;
        let noProduct = `<tr class="bg-background no-data">
                            <td colspan="6"
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                {{ translate('No data') }}
                            </td>
                        </tr>`;

        $('.damage-tbody').empty().html(noProduct);
        $('.damage-search-results').empty().html(noSearch);
    });
</script>
