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
        debouncedProductsSearch()
        $(".stock-product-search").show();
    });

    // qty stepper controls
    function updateStockQtyValue($input, step) {
        const min = parseFloat($input.attr('min')) || 1;
        const currentValue = parseFloat($input.val()) || min;
        const nextValue = Math.max(min, currentValue + step);
        $input.val(nextValue);
    }

    // expose for inline onclick handlers
    window.adjustStockQty = function($this, step) {
        const $qtyInput = $($this).closest('.stock-qty-control').find('input[name="stockQty[]"]');
        updateStockQtyValue($qtyInput, step);
    };

    window.normalizeStockQty = function($this) {
        const $input = $($this);
        const min = parseFloat($input.attr('min')) || 1;
        const currentValue = parseFloat($input.val()) || min;
        $input.val(Math.max(min, currentValue));
    };

    // search products
    const debouncedProductsSearch = debounce(getProducts, 300);
    $('.stocks-product-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedProductsSearch()
    }));

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
            url: '{{ route(routePrefix() . '.stockAdjustments.getProducts') }}',
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
                            <select class="theme-input h-auto p-3" name="actions[]"  >
                                <option value="addition">{{ translate('Addition') }}</option>
                                <option value="deduction">{{ translate('Deduction') }}</option>
                            </select>
                        </td>  
                        
                        <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[170px]">
                            <div class="stock-qty-control flex items-center gap-2">
                                <button
                                    type="button"
                                    class="h-[42px] w-[42px] rounded-md border border-border bg-background text-muted hover:bg-background-hover"
                                    onclick="adjustStockQty(this, -1)">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input
                                    type="number"
                                    name="stockQty[]"
                                    min="1"
                                    value="1"
                                    class="theme-input text-center"
                                    oninput="normalizeStockQty(this)" />
                                <button
                                    type="button"
                                    class="h-[42px] w-[42px] rounded-md border border-border bg-background text-muted hover:bg-background-hover"
                                    onclick="adjustStockQty(this, 1)">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </td>

                        @if (useInventory())
                            <td class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap min-w-[150px] expiry-date-cell">
                                <div class="date-picker date-picker--single">
                                    <input
                                        type="text"
                                        name="expiryDates[]"
                                        value=""
                                        placeholder="{{ translate('Expiry Date') }}"
                                        class="theme-input stock-expiry-date-input"
                                        autocomplete="off" />
                                </div>
                            </td>
                        @endif

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

        if ('{{ useInventory() }}' === '1') {
            const $newDatePicker = $('.stocks-tbody tr:last input[name="expiryDates[]"]');
            initializeExpiryDatePicker($newDatePicker);
        }

        // Apply action-driven expiry visibility for this row
        $(`select[name="actions[]"]`).last().trigger('change');
        updateSupplierVisibility();
    }

    function updateSupplierVisibility() {
        if ('{{ useInventory() }}' !== '1') {
            return;
        }

        const hasAddition = $('select[name="actions[]"]').map(function() {
            return $(this).val();
        }).get().some(function(val) {
            return val === 'addition';
        });

        const $supplierWrapper = $('.supplier-field-wrapper');
        const $supplierSelect = $supplierWrapper.find('select[name="supplier_id"]');
        if (hasAddition) {
            $supplierWrapper.show();
            $supplierSelect.prop('disabled', false);
        } else {
            $supplierWrapper.hide();
            $supplierSelect.val('').prop('disabled', true);
        }
    }

    // Handle action change to show/hide expiry date and supplier
    $(document).on('change', 'select[name="actions[]"]', function() {
        if ('{{ useInventory() }}' !== '1') {
            return;
        }

        const action = $(this).val();
        const $expiryCell = $(this).closest('tr').find('.expiry-date-cell');
        const $expiryInput = $expiryCell.find('input[name="expiryDates[]"]');

        if (action === 'addition') {
            $expiryCell.show();
            initializeExpiryDatePicker($expiryInput);
        } else {
            $expiryCell.hide();
            $expiryInput.val('');
        }

        updateSupplierVisibility();
    });

    // removeOrderItem(this)
    function removeOrderItem($this) {
        $($this).closest('tr').remove();
        updateSupplierVisibility();
    }

    // on warehouse change
    $('[name=warehouse_id]').on('change', function() {
        let noSearch = `<div class="flex justify-center items-center py-[35px]">
                        {{ translate('No results') }}
                    </div>`;
        let noProduct = `<tr class="bg-background no-data">
                            <td colspan="${'{{ useInventory() }}' === '1' ? 7 : 6}"
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                {{ translate('No data') }}
                            </td>
                        </tr>`;

        $('.stocks-tbody').empty().html(noProduct);
        $('.stock-search-results').empty().html(noSearch);
    })

    function initializeExpiryDatePicker($input) {
        if (!$input || !$input.length || $input.data('daterangepicker')) {
            return;
        }

        $input.daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            drops: 'auto',
            autoApply: true,
            autoUpdateInput: false,
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

        $input.on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $input.on('cancel.daterangepicker', function() {
            $(this).val('');
        });
    }

    // Initialize expiry date fields on page load
    function initializeExpiryDatePickers() {
        if ('{{ useInventory() }}' === '1') {
            $('input[name="expiryDates[]"]').each(function() {
                const $input = $(this);
                const originalValue = $input.data('value') || $input.val();

                initializeExpiryDatePicker($input);

                // Set original value if exists
                if (originalValue && $input.data('daterangepicker')) {
                    $input.val(originalValue);
                    $input.data('daterangepicker').setStartDate(originalValue);
                    $input.data('daterangepicker').setEndDate(originalValue);
                }
            });
        }
    }

    $(document).ready(function() {
        initializeExpiryDatePickers();
        $('select[name="actions[]"]').trigger('change');
        updateSupplierVisibility();
    });
</script>
