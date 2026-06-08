<script>
    "use strict";

    ATE.searchKey = '';
    // Check if we're in edit mode (purchase order exists)
    ATE.isEditMode = {{ isset($purchaseOrder) ? 'true' : 'false' }};

    // Wait for DOM to be ready before attaching event listeners
    $(document).ready(function() {
        // submit form
        $('.po-form').on('submit', function(e) {
            let selectedVariationIds = $("input[name='selectedVariationIds[]']")
                .map(function() {
                    return $(this).val();
                }).get();

            if (selectedVariationIds.length < 1) {
                notifyMe('error', '{{ translate('Please select at least one product') }}');
                e.preventDefault();
            }
        });

        // search products - define debounced function FIRST
        const debouncedProductSearch = debounce(getProducts, 300);

        // show search result ui
        $('.purchase-order-product-search-input').on('click', function() {
            debouncedProductSearch()
            $(".po-product-search").show();
        });

        // search products on input
        $('.purchase-order-product-search-input').on('input', ((e) => {
            ATE.searchKey = e.target.value
            debouncedProductSearch()
        }));

        // warehouse change handler
        $('[name=warehouse_id]').on('change', function() {
            let noSearch = `<div class="flex justify-center items-center py-[35px]">
                            {{ translate('No results') }}
                        </div>`;
            let noProduct = `<tr class="bg-background no-data">
                                <td colspan="${ATE.isEditMode && '{{ useInventory() }}' === '1' ? '10' : '9'}"
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                    {{ translate('No data') }}
                                </td>
                            </tr>`;

            $('.purchase-orders-tbody').empty().html(noProduct);
            $('.po-search-results').empty().html(noSearch);
        });
    });

    // get products
    function getProducts() {
        let warehouseId = ($('[name=warehouse_id]').val());
        if (warehouseId == '') {
            notifyMe('warning', '{{ translate('Please select warehouse') }}')
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
            url: '{{ route(routePrefix() . '.purchase-orders.getProducts') }}',
            data: {
                searchKey: ATE.searchKey,
                selectedVariationIds: selectedVariationIds,
                warehouseId: warehouseId,
            },
            success: function(data) {
                $('.po-search-results').html(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
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

        let warehouseId = ($('[name=warehouse_id]').val());

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
                $(".purchase-order-product-search-input input").focus();
                $('.po-search-results').append(data);
            }
        });
    }

    // handle po product click
    function handlePoProductClick($this) {
        let
            variationId = 0,
            length = 0,
            name = '',
            unit = '',
            defaultUnitPrice = 0,
            stock = 0,
            discount = 0,
            tax = 0,
            subtotal = 0;

        $('.purchase-orders-tbody .no-data')?.remove();

        variationId = $($this).data('variation-id');
        length = $('.purchase-orders-tbody').children().length + 1;
        name = $($this).data('name');
        unit = $($this).data('unit');
        stock = $($this).data('stock');
        defaultUnitPrice = $($this).data('price');
        discount = $($this).data('discount');
        tax = $($this).data('tax');
        subtotal = $($this).data('subtotal');

        let tr = `<tr class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                             <span class="tr-length">${length}</span>
                            <input type="hidden" name="selectedVariationIds[]" value="${variationId}"> 
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 w-[200px] tr-name">
                          ${name} 
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap tr-default-unit-price">
                            ${parseFloat(defaultUnitPrice).toFixed(3)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground tr-current-stock"> 
                            ${stock} <span class="tr-unit">${unit}</span>
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]"> 
                            <x-backend.inputs.number name="unitPrice[]" min="0" onkeyup="calculateSubtotal(this)"
                                step="0.001" value="${defaultUnitPrice.toFixed(3)}" />
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                            <x-backend.inputs.number name="stockQty[]" min="1" value="1" onkeyup="calculateSubtotal(this)" />
                        </td>
                        ${ATE.isEditMode && '{{ useInventory() }}' === '1' ? `
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px]">
                            <x-backend.inputs.datepicker labelInline="true"
                                type="single"
                                name="expiryDates[]"
                                placeholder="{{ translate('Expiry Date') }}" value="" />
                        </td>` : ''}
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <input type="hidden" name="discount[]" value="${discount}">
                            <input type="hidden" name="discountPrice[]" value="${discount}">
                            <span class="tr-discount">${parseFloat(discount).toFixed(3)}</span>
                        </td>
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap">
                            <input type="hidden" name="tax[]" value="${tax}">
                            <input type="hidden" name="taxPrice[]" value="${tax}">
                            <span class="tr-tax">${parseFloat(tax).toFixed(3)}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground"> 
                            <input type="hidden" name="subtotal[]" value="${subtotal}">
                            <span class="tr-subtotal">${parseFloat(subtotal).toFixed(3)}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                            <button type="button" class="text-md flex items-center justify-center"
                                onclick="removeOrderItem(this)">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </td>
                    </tr>`;

        $('.purchase-orders-tbody').append(tr);
        $($this).remove();
        $(".po-product-search").hide();

        // Initialize date picker for the new expiry date field
        if (ATE.isEditMode && '{{ useInventory() }}' === '1') {
            const $newDatePicker = $('.purchase-orders-tbody tr:last .expiry-date-picker input');
            const currentStatus = $('[name=status]').val();
            const isReceived = currentStatus === 'received';
            
            $newDatePicker.daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                drops: 'auto',
                autoApply: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });
            
            if (!isReceived) {
                $newDatePicker.prop('disabled', true);
                $newDatePicker.closest('td').addClass('opacity-50');
            }
        }

        calculateSummary();
    }

    // calculate subtotal 
    function calculateSubtotal($this) {
        let oldTr = $($this).closest('tr');
        let
            unitPrice = parseFloat($(oldTr).find('input[name="unitPrice[]"]').val()) || 0,
            stockQty = parseFloat($(oldTr).find('input[name="stockQty[]"]').val()) || 0,

            discount = parseFloat($(oldTr).find('input[name="discount[]"]').val()),
            tax = parseFloat($(oldTr).find('input[name="tax[]"]').val()),
            subtotal = (unitPrice * stockQty) + (tax * stockQty) - (discount * stockQty);

        $(oldTr).find("input[name='discountPrice[]']").val(discount * stockQty);
        $(oldTr).find('.tr-discount').html(parseFloat(discount * stockQty).toFixed(3));

        $(oldTr).find("input[name='taxPrice[]']").val(tax * stockQty);
        $(oldTr).find('.tr-tax').html(parseFloat(tax * stockQty).toFixed(3));

        $(oldTr).find("input[name='subtotal[]']").val(subtotal);
        $(oldTr).find('.tr-subtotal').html(parseFloat(subtotal).toFixed(3));
        calculateSummary();
    }

    // calculate summary
    function calculateSummary() {
        let taxPercentage = $('input[name="tax"]').val() || 0;
        let discount = $('input[name="discount"]').val() || 0;
        let shipping = $('input[name="shipping"]').val() || 0;

        let subtotal = $("input[name='subtotal[]']")
            .map(function() {
                return $(this).val();
            }).get();

        if (subtotal.length > 0) {
            subtotal = subtotal.reduce(function(a, b) {
                return (parseFloat(a) + parseFloat(b)).toFixed(3);
            });
        } else {
            subtotal = 0;
        }

        let subtotalWithTax = parseFloat(subtotal) + parseFloat((parseFloat(subtotal) * parseFloat(taxPercentage)) /
            100);

        let tax = parseFloat(subtotalWithTax - subtotal).toFixed(3);
        let grandTotal = (parseFloat(subtotalWithTax) - parseFloat(discount) + parseFloat(shipping)).toFixed(3);

        $('.tax-summary-percentage').html(taxPercentage);
        $('.tax-summary').html(tax);
        $('.discount-summary').html(parseFloat(discount).toFixed(3));
        $('.grand-total-summary').html(grandTotal)
         $('.shipping-summary').html(parseFloat(shipping).toFixed(3));
    }

    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
        calculateSummary();
    }

    // on warehouse change
    // Handle status change to enable/disable expiry date fields
    $('[name=status]').on('change', function() {
        // Re-initialize all expiry date pickers when status changes
        initializeExpiryDatePickers();
    });

    // Initialize expiry date fields separately with no default value
    function initializeExpiryDatePickers() {
        const currentStatus = $('[name=status]').val();
        const isReceived = currentStatus === 'received';

        if ('{{ useInventory() }}' === '1') {
            // Initialize datepickers that were skipped by global JS (data-skip-datepicker-init)
            $('input[data-skip-datepicker-init]').each(function() {
                const $input = $(this);
                const originalValue = $input.data('value') || $input.val();
                const expiryDateCell = $input.closest('td');

                // Initialize date picker with no default value (skipped by global init)
                $input.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    drops: 'auto',
                    autoApply: true,
                    autoUpdateInput: false, // Don't auto-populate input - key change!
                    locale: {
                        format: 'DD-MM-YYYY'
                    }
                });

                // Handle date selection to manually update input
                $input.on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY'));
                });

                // Clear input when datepicker is cancelled
                $input.on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

                // Handle status-based behavior - just ensure empty value for non-received status
                if (!isReceived) {
                    $input.val(''); // Ensure empty for non-received status
                } else {
                    // Only set value if we have one for received status
                    if (originalValue) {
                        $input.val(originalValue);
                        $input.data('daterangepicker').setStartDate(originalValue);
                        $input.data('daterangepicker').setEndDate(originalValue);
                    }
                }
            });
        }
    }

    // Initialize expiry date fields on page load
    $(document).ready(function() {
        initializeExpiryDatePickers();
        
        // Handle status changes
        $('[name=status]').on('change', function() {
            const newStatus = $(this).val();
            const isReceived = newStatus === 'received';

            if ('{{ useInventory() }}' === '1') {
                // Handle status changes for skipped datepickers - just clear values for non-received status
                $('input[data-skip-datepicker-init]').each(function() {
                    const $input = $(this);

                    if (!isReceived) {
                        $input.val('');
                        if ($input.data('daterangepicker')) {
                            $input.data('daterangepicker').setStartDate(null);
                            $input.data('daterangepicker').setEndDate(null);
                        }
                    }
                });
            }
        });
    });
</script>
