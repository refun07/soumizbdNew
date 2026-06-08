<script>
    "use strict";

    ATE.searchKey = '';

    // submit form
    $('.stocks-form').on('submit', function(e) {
        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        if (selectedVariationIds.length < 1) {
            notifyMe('error', '{{ translate('Please select at least one product') }}');
            e.preventDefault();
        }
    });

    // show search result ui
    $('.stocks-product-search-input').on('click', function() {
        debouncedProductSearch()
        $(".stock-product-search").show();
    });

    // search products
    const debouncedProductSearch = debounce(getProducts, 300);
    $('.stocks-product-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedProductSearch()
    }));

    // get products
    function getProducts() {

        let warehouseId = ($('[name=from_warehouse_id]').val());
        if (warehouseId == '') {
            notifyMe('warning', '{{ translate('Please select from warehouse') }}')
            return;
        }

        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.stockTransfers.getProducts') }}',
            data: {
                searchKey: ATE.searchKey,
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $('.stock-search-results').html(data);
            }
        });
    }

    // load More Products
    function loadMorePoProducts($this, url) {
        $('.load-more-spin').removeClass('hidden');

        let selectedVariationIds = $("input[name='selectedVariationIds[]']")
            .map(function() {
                return $(this).val();
            }).get();

        let warehouseId = ($('[name=from_warehouse_id]').val());

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
                $(".stocks-product-search-input input").focus();
                $('.stock-search-results').append(data);
            }
        });
    }

    // hadnle po product click
    function handlePoProductClick($this) {
        let
            variationId = 0,
            length = 0,
            name = '',
            unit = '',
            stock = 0;

        $('.stocks-tbody .no-data')?.remove();

        variationId = $($this).data('variation-id');
        length = $('.stocks-tbody').children().length + 1;
        name = $($this).data('name');
        unit = $($this).data('unit');
        stock = $($this).data('stock');

        let tr = `<tr class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                             <span class="tr-length">${length}</span>
                            <input type="hidden" name="selectedVariationIds[]" value="${variationId}"> 
                        </td>
                        
                        <td class="text-sm text-foreground font-light px-6 py-4 w-[200px] tr-name">
                          ${name} 
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock"> 
                            ${stock} <span class="tr-unit">${unit}</span>
                        </td>
                        
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <x-backend.inputs.number name="stockQty[]" min="1" value="1" max="${stock}" />
                        </td> 

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeOrderItem(this)">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;

        $('.stocks-tbody').append(tr);
        $($this).remove();
        $(".stock-product-search").hide();
    }

    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
    }

    // on warehouse change
    $('[name=from_warehouse_id]').on('change', function() {
        let noSearch = `<div class="flex justify-center items-center py-[35px]">
                        {{ translate('No results') }}
                    </div>`;
        let noProduct = `<tr class="bg-background no-data">
                            <td colspan="9"
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                {{ translate('No data') }}
                            </td>
                        </tr>`;

        $('.stocks-tbody').empty().html(noProduct);
        $('.stock-search-results').empty().html(noSearch);
    })
</script>
