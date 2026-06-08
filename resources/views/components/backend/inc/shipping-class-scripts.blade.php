<script>
    "use strict";

    ATE.searchKey = '';

    // submit form
    // $('.shipping-class-form').on('submit', function(e) {
    //     let selectedVariationIds = $("input[name='selectedVariationIds[]']")
    //         .map(function() {
    //             return $(this).val();
    //         }).get();

    //     if (selectedVariationIds.length < 1) {
    //         notifyMe('error', '{{ translate('Please select at least one product') }}');
    //         e.preventDefault();
    //     }
    // });

    // show search result ui
    $('.shipping-class-product-search-input').on('click', function() {
        debouncedProductSearch()
        $(".shipping-class-product-search").show();
    });

    // search products
    const debouncedProductSearch = debounce(getProducts, 300);
    $('.shipping-class-product-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedProductSearch()
    }));

    // get products
    function getProducts() {


        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route('shipping-classes.get-products') }}',
            data: {
                searchKey: ATE.searchKey,
            },
            success: function(data) {
                $('.shipping-class-product-search-results').html(data);
            }
        });
    }

    // load More Products
    function loadMorePoProducts($this, url) {
        $('.load-more-spin').removeClass('hidden');

        let selectedProductIds = $("input[name='selectedProductIds[]']")
            .map(function() {
                return $(this).val();
            }).get();


        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: url,
            data: {
                selectedProductIds: selectedProductIds,
            },
            success: function(data) {
                $($this).closest('div').remove();
                $(".shipping-class-product-search-input input").focus();
                $('.shipping-class-product-search-results').append(data);
            }
        });
    }

    // hadnle po product click
    function handlePoProductClick($this) {
        let
            productId = 0,
            name = '';

        $('.purchase-orders-tbody .no-data')?.remove();

        productId = $($this).data('product-id');
        name = $($this).data('name');

        let tr = `<tr class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                       
                        <td class="text-sm text-foreground font-light py-4  tr-name">
                            <input type="hidden" name="selectedProductIds[]" value="${productId}"> 
                          ${name} 
                        </td>
                        <td class="py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeOrderItem(this)">
                                <i class="fa-regular fa-trash-can text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;
        $('.empty-tr').hide();
        $('.shipping-class-selected-product-tbody').append(tr);
        $($this).remove();
        $(".shipping-class-product-search").hide();
    }

    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
        const remainingRows = $('.shipping-class-selected-product-tbody tr').length;

        if (remainingRows === 1) {
            $('.empty-tr').show();
        } else {
            $('.empty-tr').hide();
        }
    }
</script>
