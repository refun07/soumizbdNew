<script>
    "use strict";

    // switch markup based on selection
    function isVariantProduct(el) {
        // Check if we're converting from variant to non-variant
        const hasVariations = $(el).data('has-variations') === true || $(el).data('has-variations') === 'true';
        const variationCount = parseInt($(el).data('variation-count') || 0);
        const isChecked = $(el).is(':checked');

        // If unchecking (converting to non-variant) and product has existing variations
        if (!isChecked && hasVariations && variationCount > 0) {
            // Store reference to checkbox for later use
            window.pendingVariantToggle = el;
            
            // Show the warning modal
            MicroModal.show('variant-conversion-warning-modal', {
                onShow: function(modal) {
                    // Modal is now visible
                },
                onClose: function(modal) {
                    // Modal closed - cleanup
                    window.pendingVariantToggle = null;
                }
            });
            
            // Revert the checkbox state immediately - will be updated based on user choice
            $(el).prop('checked', true);
            return;
        }

        // Normal toggle behavior (no warning needed)
        toggleVariantUI(el);
    }

    // Handle the actual UI toggle
    function toggleVariantUI(el) {
        $(".hasVariation").hide();
        $(".noVariation").hide();

        if ($(el).is(':checked')) {
            $(".hasVariation").show();
            generateVariationCombinations();

            // remove required field for non variations
            $("#price").removeAttr('required', true);
            $("#stock_qty").removeAttr('required', true);
            $("#sku").removeAttr('required', true);

            // add required field for variations  
            $(".chosen_variations").attr('required', true);

        } else {
            $(".noVariation").show();
            // add required field for non variations  
            $("#price").attr('required', true);
            $("#stock_qty").attr('required', true);
            $("#sku").attr('required', true);

            // remove required field for variations  
            $(".chosen_variations").removeAttr('required', true);
            hideBulkUpdateSection();
        }
    }

    function showBulkUpdateSection() {
        $('#bulk_update_section').show();
    }

    function hideBulkUpdateSection() {
        $('#bulk_update_section').hide();
        clearBulkUpdate();
    }

    function shippingCharges(e) {
        const container = document.getElementById('shipping-charges-display');
        if (!container || !e || !e.options || e.selectedIndex < 0) return;

        const selectedOption = e.options[e.selectedIndex];
        const charges = selectedOption.getAttribute('data-shipping-charges');
        container.innerHTML = '';
        if (!charges) return;

        const parsedCharges = JSON.parse(charges);

        // console.log('Shipping Charges:', parsedCharges);
        parsedCharges.forEach(charge => {
            const row = document.createElement('div');
            row.classList.add('flex', 'justify-between', 'text-gray-600');

            row.innerHTML = `
            <span>${charge.zone?.name}</span>
            <span>${charge.charge}Tk</span>
        `;

            container.appendChild(row);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const shippingClassSelect = document.querySelector('select[name="shipping_class_id"]');
        if (shippingClassSelect) {
            shippingCharges(shippingClassSelect);
        }
    });

    // variation combinations
    function generateVariationCombinations() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.products.generateVariationCombinations') }}',
            data: $('#product-form').serialize(),
            success: function(data) {
                $('#variation_combination').html(data);
                initAccordion();
                initMicroModal();
                showBulkUpdateSection();
            }
        });
    }


    // get values for selected variations
    function getVariationValues(e) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: "POST",
            data: {
                variation_id: $(e).val()
            },
            url: '{{ route(routePrefix() . '.products.getVariationValues') }}',
            success: function(data) {
                $(e).closest('.grid').find('.variationvalues').html(data);
                generateVariationCombinations();
                initSelect2();
            }
        });
    }


    // add another variation
    function addAnotherVariation() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: $('#product-form').serialize(),
            url: '{{ route(routePrefix() . '.products.newVariation') }}',
            success: function(data) {
                if (data.count > 0) {
                    $('.chosen_variation_options').append(data.view);
                    initSelect2();
                    generateVariationCombinations();
                } else if (data.count == -1) {
                    notifyMe('warning', '{{ translate('Please select a variation.') }}');
                }
            }
        });
    }

    // remove variation
    function removeVariation($this) {
        var totalRows = $('.chosen_variation_options .grid').length;
        if (totalRows <= 1) {
            notifyMe('warning', '{{ translate('At least one attribute is required.') }}');
            return;
        }
        $this.closest('.grid').remove();
        generateVariationCombinations();
    }

    // category-selector: each category checkbox should only toggle itself.
    $(document).off('change.productCategorySelector', '.category-selector:checkbox');

    function initProductCategorySearch() {
        const input = document.querySelector('[data-category-search]');
        const tree = document.querySelector('.cateogry-tree');
        const emptyState = document.querySelector('[data-category-search-empty]');

        if (!input || !tree || input.dataset.searchInitialized === 'true') {
            return;
        }

        input.dataset.searchInitialized = 'true';

        const normalize = (value) => (value || '').toString().trim().toLowerCase();

        const syncToggleIcon = (item, isOpen) => {
            const icon = item.querySelector(':scope > .category-toggler i');

            item.classList.toggle('open', isOpen);

            if (icon) {
                icon.classList.toggle('fa-chevron-down', !isOpen);
                icon.classList.toggle('fa-chevron-up', isOpen);
            }
        };

        const resetSearchState = () => {
            tree.querySelectorAll('.category').forEach((item) => {
                item.hidden = false;
                item.classList.remove('category-search-match');

                if (item.dataset.searchOpened === 'true') {
                    syncToggleIcon(item, false);
                    delete item.dataset.searchOpened;
                }
            });

            emptyState?.classList.add('hidden');
        };

        const filterItem = (item, query) => {
            const label = item.querySelector(':scope > label');
            const childItems = Array.from(item.querySelectorAll(':scope > ul > .category'));
            const isOwnMatch = normalize(label?.textContent).includes(query);
            const hasMatchingChild = childItems.some((child) => filterItem(child, query));
            const isVisible = isOwnMatch || hasMatchingChild;

            item.hidden = !isVisible;
            item.classList.toggle('category-search-match', isOwnMatch && query.length > 0);

            if (query.length > 0 && hasMatchingChild) {
                syncToggleIcon(item, true);
                item.dataset.searchOpened = 'true';
            } else if (item.dataset.searchOpened === 'true') {
                syncToggleIcon(item, false);
                delete item.dataset.searchOpened;
            }

            return isVisible;
        };

        input.addEventListener('input', function() {
            const query = normalize(this.value);

            if (!query) {
                resetSearchState();
                return;
            }

            const rootItems = Array.from(tree.querySelectorAll('.category-tree-data > .category'));
            const hasResults = rootItems.some((item) => filterItem(item, query));

            emptyState?.classList.toggle('hidden', hasResults);
        });
    }

    initProductCategorySearch();

    // initCategoryCreateModal
    function initCategoryCreateModal() {
        $('.category-create-modal').on('click', async function() {

            $('#category-create').empty();
            $('.loader').removeClass('hidden');

            let data = {
                _token: '{{ csrf_token() }}',
            };

            // category-create
            const res = await $.ajax({
                url: '{{ route('admin.categories.initCreateModal') }}',
                type: "GET",
                data: data,
            });

            $('#category-create').html(res);
            $('.loader').addClass('hidden');
            initSelect2();
            handleCategoryCreateFormSubmit()
        })
    }
    initCategoryCreateModal();

    // handle category form submit
    function handleCategoryCreateFormSubmit() {
        $('.category-form-modal').on('submit', function(e) {
            e.preventDefault();

            // Get all checked category IDs
            var checkedCategoryIds = $('input[name="category_ids[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            // Convert the array to a string representation (comma-separated values)
            var checkedCategoryIds = checkedCategoryIds.join(',');

            // do what you like with the input
            let $input = $('<input type="hidden" name="selected_categories"/>').val(checkedCategoryIds);
            // append to the form
            $('.category-form-modal').append($input);

            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.category-form-modal').serialize(),
                url: '{{ route('admin.categories.store') }}',
                success: function(data) {
                    $('.category-tree-data').empty().html(data);
                    initCategoryTree();
                    initProductCategorySearch();
                    document.querySelector('[data-category-search]')?.dispatchEvent(new Event('input'));
                    MicroModal.close('category-create-modal');
                    notifyMe('success', '{{ translate('Category added successfully.') }}');
                }
            });
        })
    }

    // handle brand form submit
    function handleBrandCreateFormSubmit() {
        $('.brand-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.brand-form-modal').serialize(),
                url: '{{ route('admin.brands.store') }}',
                success: function(data) {
                    $('.brand-tree-data').empty().html(data);
                    document.querySelector('[data-brand-search]')?.dispatchEvent(new Event('input'));
                    MicroModal.close('brand-create-modal');
                    notifyMe('success', '{{ translate('Brand added successfully.') }}');
                }
            });
        })
    }
    handleBrandCreateFormSubmit();

    // handle unit form submit
    function handleUnitCreateFormSubmit() {
        $('.unit-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.unit-form-modal').serialize(),
                url: '{{ route('admin.units.store') }}',
                success: function(data) {
                    $('.unit-tree-data').empty().html(data);
                    initSelect2();
                    MicroModal.close('unit-create-modal');
                    notifyMe('success', '{{ translate('Unit added successfully.') }}');
                }
            });
        })
    }
    handleUnitCreateFormSubmit();

    // handle tag form submit
    function handleTagCreateFormSubmit() {
        $('.tag-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.tag-form-modal').serialize(),
                url: '{{ route('admin.tags.store') }}',
                success: function(data) {
                    $('.tag-tree-data').empty().html(data);
                    initSelect2();
                    MicroModal.close('tag-create-modal');
                    notifyMe('success', '{{ translate('Tag added successfully.') }}');
                }
            });
        })
    }
    handleTagCreateFormSubmit();

    // handle badge form submit
    function handleBadgeCreateFormSubmit() {
        $('.badge-form-modal').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: $('.badge-form-modal').serialize(),
                url: '{{ route('admin.badges.store') }}',
                success: function(data) {
                    $('.badge-tree-data').empty().html(data);
                    MicroModal.close('badge-create-modal');
                    notifyMe('success', '{{ translate('Badge added successfully.') }}');
                }
            });
        })
    }


    handleBadgeCreateFormSubmit();

    function setBulkVariationField(fieldName, value) {
        const $variationContainer = $('#variation_combination');
        const $fields = $variationContainer.find('[name]').filter(function() {
            const name = this.getAttribute('name') || '';
            return name.startsWith('variations[') && name.endsWith(`[${fieldName}]`);
        });

        $fields.val(value).trigger('input').trigger('change');
        return $fields.length;
    }

    function applyBulkUpdate(showNotification = true) {
        const bulkPrice = $('#bulk_price').val();
        const bulkDiscountValue = $('#bulk_discount_value').val();
        const bulkDiscountType = $('#bulk_discount_type').val();
        let updatedFieldCount = 0;

        if (!$('#variation_combination .product-variations').length) {
            notifyMe('warning', '{{ translate('No variations found to update.') }}');
            return;
        }

        if (bulkPrice !== '') {
            updatedFieldCount += setBulkVariationField('price', bulkPrice);
        }

        if (bulkDiscountValue !== '') {
            updatedFieldCount += setBulkVariationField('discount_value', bulkDiscountValue);
        }

        if (bulkDiscountType) {
            updatedFieldCount += setBulkVariationField('discount_type', bulkDiscountType);
        }

        if (showNotification) {
            if (!updatedFieldCount) {
                notifyMe('warning', '{{ translate('No variation fields found to update.') }}');
                return;
            }

            notifyMe('success', '{{ translate('Variations updated successfully.') }}');
        }
    }

    function clearBulkUpdate() {
        $('#bulk_price').val('');
        $('#bulk_discount_value').val('');
        $('#bulk_discount_type').val('flat');
    }

    $(document).off('input.bulkVariations change.bulkVariations', '#bulk_price, #bulk_discount_value, #bulk_discount_type')
        .on('input.bulkVariations change.bulkVariations', '#bulk_price, #bulk_discount_value, #bulk_discount_type', function() {
            applyBulkUpdate(false);
        });

    // Initialize on page load - for existing products show bulk update directly, for new products call AJAX
    $(document).ready(function() {
        if ($('#is_variant').is(':checked')) {
            if ($('input[name="product_id"]').val()) {
                // Existing product - show bulk update section directly, data already rendered
                showBulkUpdateSection();
            } else {
                // New product - generate combinations via AJAX
                generateVariationCombinations();
            }
        }

        // Handle variant conversion warning modal - Confirm button
        $('#variant-conversion-confirm').on('click', function() {
            if (window.pendingVariantToggle) {
                const $checkbox = $(window.pendingVariantToggle);
                
                // Update data attributes to indicate conversion is in progress
                $checkbox.data('has-variations', false);
                
                // Uncheck the checkbox
                $checkbox.prop('checked', false);
                
                // Apply the UI toggle
                toggleVariantUI(window.pendingVariantToggle);
                
                // Close the modal
                MicroModal.close('variant-conversion-warning-modal');
            }
        });

        // Handle variant conversion warning modal - Cancel button
        $('#variant-conversion-cancel').on('click', function() {
            // Keep checkbox checked (already done when showing modal)
            window.pendingVariantToggle = null;
            MicroModal.close('variant-conversion-warning-modal');
        });
    });
</script>
