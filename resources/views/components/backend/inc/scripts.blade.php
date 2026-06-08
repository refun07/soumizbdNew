<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/swiper.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/nouislider.min.js') }}"></script>
<script src="{{ asset('js/uppy.min.js') }}"></script>
<script src="{{ asset('js/micromodal.min.js') }}"></script>
<script src="{{ asset('js/jodit.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/footable.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('js/accodion.min.js') }}"></script>
{{-- floating ui js files --}}
<script src="{{ asset('js/floating-ui-core-1.6.0.min.js') }}"></script>
<script src="{{ asset('js/floating-ui-dom-1.6.3.min.js') }}"></script>

<script>
    "use strict";

    ATE.local = {
        nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
        nothing_found: '{!! translate('Nothing found', null, true) !!}',
        choose_file: '{{ translate('Choose file') }}',
        file_selected: '{{ translate('File selected') }}',
        files_selected: '{{ translate('Files selected') }}',
        add_more_files: '{{ translate('Add more files') }}',
        adding_more_files: '{{ translate('Adding more files') }}',
        drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
        browse: '{{ translate('Browse') }}',
        upload_complete: '{{ translate('Upload complete') }}',
        upload_paused: '{{ translate('Upload paused') }}',
        resume_upload: '{{ translate('Resume upload') }}',
        pause_upload: '{{ translate('Pause upload') }}',
        retry_upload: '{{ translate('Retry upload') }}',
        cancel_upload: '{{ translate('Cancel upload') }}',
        uploading: '{{ translate('Uploading') }}',
        processing: '{{ translate('Processing') }}',
        complete: '{{ translate('Complete') }}',
        file: '{{ translate('File') }}',
        files: '{{ translate('Files') }}',
    }

    // change language
    function changeLocaleLanguage(e) {
        var locale = e.dataset.flag;
        $.post("{{ route(routePrefix() . '.changeLanguage') }}", {
            _token: '{{ csrf_token() }}',
            locale: locale
        }, function(data) {
            location.reload();
        });
    }

    ATE.data = {
        csrf: '{{ csrf_token() }}',
        appUrl: '{{ url('/') }}',
        fileBaseUrl: $('meta[name="file-base-url"]').attr("content"),
    };

    const dateFormatter = (date, formatString = 'DD MMM, YYYY') => {
        return moment(date).format(formatString);
    }

    const debounce = function(func, wait, immediate) {
        let timeout;
        return function() {
            let context = this,
                args = arguments;
            let later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            let callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args)
        }
    }

    // category tree for product edit page
    function initCategoryTree() {
        $('.has-subcategory .category-toggler').on('click', function() {
            $(this).parent().toggleClass('open');
            $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        })
    }
    initCategoryTree();

    // init accordion
    function initAccordion() {
        const accordionMarkup = document.querySelectorAll('.init-accordion')
        if (accordionMarkup.length) {
            new Accordion(Array.from(accordionMarkup), {
                duration: 200,
                showMultiple: true,
                // openOnInit: [0]
            });
        }
    }
    initAccordion();


    // ajax toast
    function notifyMe(level, message) {
        if (level == 'danger') {
            level = 'error';
        }
        toastr.options = {
            closeButton: true,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "3000",
            hideDuration: "1000",
            timeOut: "3000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut",
        };
        toastr[level](message);
    }

    // laravel flash toast messages
    @foreach (session('flash_notification', collect())->toArray() as $message)
        notifyMe("{{ $message['level'] }}", "{{ $message['message'] }}");
    @endforeach

    // update is_active status
    function initActiveStatusUpdate() { 
        $(document).off('change', 'input[name=isActiveCheckbox]');
        $(document).on('change', 'input[name=isActiveCheckbox]', async function () {
            let data = {
                _token: '{{ csrf_token() }}',
                id: $(this).val(),
                isActive: $(this).data('status') == 1 ? 0 : 1,
            }
            const res = await $.ajax({
                url: `${$(this).data('route')}`,
                type: "POST",
                data: data,
            });

            if (res.success == true && res.status == 200) {
                notifyMe('success', res.message);
            } else {
                notifyMe('success', `{{ translate('Something went wrong') }}`);
            }
        })
    }
    initActiveStatusUpdate();

    // update is_default status
    function initDefaultStatusUpdate() {
        $("input[name=isDefaultCheckbox]").on('change', async function() {
            let data = {
                _token: '{{ csrf_token() }}',
                id: $(this).val(),
                isDefault: $(this).data('status') == 1 ? 0 : 1,
            }
            const res = await $.ajax({
                url: `${$(this).data('route')}`,
                type: "POST",
                data: data,
            });

            if (res.success == true && res.status == 200) {
                notifyMe('success', res.message);
                window.location.reload();
            } else {
                notifyMe('success', `{{ translate('Something went wrong') }}`);
            }
        })
    }
    initDefaultStatusUpdate();

    // init confrim modal
    function initConfirmModal() {
        $('.confirm-modal').on('click', function() {
            let url = $(this).data('href');
            let title = $(this).data('title');
            let text = $(this).data('text');
            let method = $(this).data('method');
            let buttonText = $(this).data('button-text') || 'Delete';
            let buttonStyle = $(this).data('button-style');

            $('#confirm-modal form').attr('action', url);
            $('#confirm-modal-title').html(title);
            $('#confirm-modal-text').html(text);
            $('#confirm-modal-method').val(method);

            let $submitBtn = $('#confirm-modal-submit');
            $submitBtn.text(buttonText);

            // Reset classes
            $submitBtn.removeClass().addClass(
                'modal__btn leading-none text-white px-10 py-2.5 rounded uppercase');

            // Apply style based on flag
            if (buttonStyle === 'primary') {
                $submitBtn.addClass('button--primary');
            } else {
                $submitBtn.addClass('bg-red-500');
            }
        })
    }
    initConfirmModal();

    // initOrderAddressModal
    function initOrderAddressModal() {
        $('.order-address-modal').on('click', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let address = $(this).data('address');

            $('.order-id').val(id);
            $('.address-type').val(type);
            $("[name='address'").val(address);

            if (type == "shipping_address") {
                let direction = $(this).data('direction');
                let phone = $(this).data('phone');
                let title = '{{ translate('Update Shipping Address') }}'
                $('#order-address-modal-title').html(title);
                $('.address-info').removeClass('hidden');

                $("[name='direction'").val(direction);
                $("[name='phone'").val(phone);
            } else {
                let title = '{{ translate('Update Billing Address') }}'
                $('#order-address-modal-title').html(title);
                $('.address-info').addClass('hidden');
            }
        })
    }
    initOrderAddressModal();

    // init payment form modal
    function initPaymentModal() {
        $('.purchase-order-payment-modal').on('click', function() {
            let payable_id = $(this).data('payable-id');
            let payable_type = $(this).data('payable-type');
            let payable_amount = $(this).data('payable-amount');

            $("input[name='payable_id'").val(payable_id);
            $("input[name='payable_type'").val(payable_type);
            $("input[name='payable_amount'").val(payable_amount);
            $("input[name='paid_amount'").attr('max', payable_amount);
        })
    }
    initPaymentModal();

    // initAssignCustomer
    function initAssignCustomer() {
        $('.customer-assignment-modal-trigger').on('click', function() {
            let name = $(this).data('name');
            let customer_id = $(this).data('id');
            let reminderDate = $(this).data('reminder-date'); 
            let nextDate = $(this).data('next-date');
            let assignedTo = $(this).data('assigned-to');
            let statusNote = $(this).data('status-note');
            let htmlNotes = $(this).data('html-notes');

            $('#customer-assignment-modal-content .activity-logs-wrapper').empty().html(htmlNotes);
            $(".customer-name").text(name);
            $("input[name='customer_id'").val(customer_id);
            $("input[name='reorder_reminder_date'").val(reminderDate);
            $("input[name='next_follow_up_date'").val(nextDate);
            $("select[name='assigned_to'").val(assignedTo).trigger('change');
            $("textarea[name='status_note'").val(statusNote);
            
        })
    }
    initAssignCustomer();

    // init payment histories modal
    function initPaymentHistoriesModal() {
        $('.purchase-order-payment-histories-modal').on('click', async function() {

            $('#purchase-order-payment-histories').empty();
            $('.loader').removeClass('hidden');

            let payable_id = $(this).data('payable-id');
            let payable_type = $(this).data('payable-type');

            let data = {
                _token: '{{ csrf_token() }}',
                payable_id: payable_id,
                payable_type: payable_type
            };

            // purchase-order-payment-histories
            const res = await $.ajax({
                url: '{{ route(routePrefix() . '.purchase-payments.index') }}',
                type: "POST",
                data: data,
            });

            $('#purchase-order-payment-histories').html(res);
            $('.loader').addClass('hidden');
        })
    }
    initPaymentHistoriesModal();

    // init purchase order view modal (ajax)
    function initPurchaseOrderViewModal() {
        const loadingHtml =
            '<div class="flex items-center justify-center py-10">' +
            '<div class="w-[14px] h-[14px] rounded-full animate-spin border-2 border-solid border-black border-t-transparent"></div>' +
            '</div>';
        const failedText = @json(translate('Failed to load purchase order details'));

        $(document).off('click', '.purchase-order-view-modal').on('click', '.purchase-order-view-modal', function() {
            const url = $(this).data('url');
            $('#purchase-order-view-modal-content').html(loadingHtml);

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
            }).done(function(res) {
                if (res && res.status === 200 && res.html) {
                    $('#purchase-order-view-modal-content').html(res.html);
                    return;
                }

                $('#purchase-order-view-modal-content').html(
                    '<p class="text-sm text-red-500">' + failedText + '</p>'
                );
            }).fail(function() {
                $('#purchase-order-view-modal-content').html(
                    '<p class="text-sm text-red-500">' + failedText + '</p>'
                );
            });
        });
    }
    initPurchaseOrderViewModal();

    function initOrderPaymentModal() {
        $('.order-payment-modal').on('click', function() {
            let payable_id = $(this).data('payable-id');
            let payable_amount = $(this).data('payable-amount');

            $("input[name='payable_id'").val(payable_id);
            $("input[name='payable_amount'").val(payable_amount);
            $("input[name='paid_amount'").val(payable_amount);
        })
    }
    initOrderPaymentModal();

    // initFootable
    function initFootable() {
        $('.footable').footable({
            breakpoints: {
                xs: 480,
                sm: 768,
                md: 992,
                lg: 1200,
                xl: 1400,
                '2xl': 1600
            },
            on: {
                'ready.ft.table': function(e, ft) {
                    initActiveStatusUpdate();
                    initDefaultStatusUpdate();
                    initMicroModal();
                    initConfirmModal();
                    initPaymentModal();
                    initAssignCustomer();
                    initPaymentHistoriesModal();
                    initPurchaseOrderViewModal();
                    initSelect2();
                    mediaManager.getPreviouslySelectedMedia();
                    initSellerPayoutModal();
                    initRefundPaymentModal();
                }
            }
        });
    }
    initFootable();

    function openAddOptionModal() {
        // Close all Select2 dropdowns first
        $('.select2-container--open').each(function() {
            $(this).prev('select').select2('close');
        });

        const modal = document.getElementById("addOptionModal");
        modal.classList.remove("hidden");
        setTimeout(() => modal.classList.add("opacity-100"), 10);
    }

    function closeAddOptionModal() {
        const modal = document.getElementById("addOptionModal");
        modal.classList.remove("opacity-100");
        setTimeout(() => modal.classList.add("hidden"), 300); // matches transition duration
    }

    function closeAddOptionModalOnBackdrop(event) {
        // Close modal when clicking on the backdrop
        if (event.target.id === "addOptionModal") {
            closeAddOptionModal();
        }
    }

    function handleCreateOption(buttonElement) {
        $('#option_name').val(''); // clear input
        $('#addOptionModal').modal('show');
    }
    // initSelect2
    function initSelect2() {

        if ($('.initSelect2').hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }

        $('.initSelect2').each(function() {
            const disableSearch = $(this).data('search'); // search disabled
            if (this.name.toString().includes('option')) {
                let variationId = this.name.toString().split("_")[1];
                document.getElementById("variation_id").value = variationId;
                const isColor = $(this).data('is-color') == 1;
                console.log(variationId, isColor);
                const element = document.getElementById("option-color-selector");
                if (isColor) {
                    element.classList.remove("hidden");
                } else {
                    element.classList.add("hidden");
                }
            }
            const selectionCssClass = $(this).data('selection-css-class') ||
                "input-select2"; // or 'multi-select2'
            const noAddNew = $(this).data('no-add-new');
            let config = {
                selectionCssClass: selectionCssClass,
                escapeMarkup: function(markup) {
                    return markup; // Let our custom HTML render
                }
            };

            if ($(this).hasClass('social-icon-select')) {
                var formatSocialIcon = function(data) {
                    if (!data.id || !data.element) {
                        return data.text;
                    }

                    var iconClass = $(data.element).data('icon-class');
                    var $option = $('<span class="inline-flex items-center gap-2"></span>');

                    if (iconClass) {
                        $('<i class="' + iconClass + ' w-4 text-center"></i>').appendTo($option);
                    }

                    $('<span></span>').text(data.text).appendTo($option);
                    return $option;
                };

                config.templateResult = formatSocialIcon;
                config.templateSelection = formatSocialIcon;
            }

            // Show "Add New" button by default, hide only when explicitly requested via data-no-add-new
            // This matches the original behavior: button appears everywhere except where marked
            const showAddNew = !noAddNew;

            if (showAddNew) {
                config.language = {
                    noResults: function() {
                        return $(`<div class="text-center text-sm">
                            No results found.
                            <button type="button" onclick="openAddOptionModal()" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded text-xs">Add New</button>
                        </div>`);
                    }
                };
            }

            if (disableSearch != undefined) {
                config.minimumResultsForSearch = -1;
            }
            $(this).select2(config);

            // fix:: this removes multiple select box (footable)
            $(this).siblings(".select2-container--default").not(':first').remove();

        })
    }
    initSelect2();

    function submitAddOption() {
        const form = $('#variationValueForm');
        const formData = form.serialize();
        const variationId = $('#variation_id').val();

        $.ajax({
            url: '/admin/api/variation-values/store',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                // Get the Select2 <select> element
                const $select = $('select[name="option_' + response.variation_id + '_choices[]"]');

                // Create new option
                const newOption = new Option(response.name, response.id, true, true); // selected

                // Append to select
                $select.append(newOption).trigger('change');

                // Clear the input field
                $('#option_name').val('');
                $('#option_color_code').val('');

                // Close modal after successful submission
                closeAddOptionModal();

                // Show success notification
                notifyMe('success', 'Option added successfully.');
            },
            error: function() {
                alert('Failed to add option.');
            }
        });
    }

    // $('#variationValueForm').on('submit', function (e) {
    //     e.preventDefault();

    //     const form = $(this);
    //     const formData = form.serialize();

    //     $.ajax({
    //         url: '/admin/variation-values/store', // or your correct Laravel route
    //         method: 'POST',
    //         data: formData,
    //         headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function (response) {
    //             closeAddOptionModal();
    //             console.log('Saved:', response);
    //             alert('Variation value added successfully!');
    //         },
    //         error: function (xhr) {
    //             console.error('Error:', xhr.responseText);
    //             alert('Something went wrong while saving.');
    //         }
    //     });
    // });



    // language badge select2
    function initBadgeSelect2() {
        $(".badge-select").select2({
            selectionCssClass: 'multi-select2',
            templateResult: bgSelect,
            templateSelection: bgSelect,
            escapeMarkup: function(m) {
                return m;
            },
        });

        function bgSelect(state) {
            var bg = $(state.element).data("bg");
            if (!bg) return state.text;
            return (
                "<div class='flex items-center justify-between w-full'><div class='me-2'>" + state.text +
                "</div><div class='rounded-full w-[18px] h-[18px]' style='background-color:" + bg +
                " !important'></div></div>"
            );
        }
    }
    initBadgeSelect2();


    // init modal
    function initMicroModal() {
        MicroModal.init({
            onShow: (modal, triggeredBy) => {
                if (modal.id === 'media-manager-modal') {
                    mediaManager.init(triggeredBy);
                }
            },
            onClose: (modal) => {
                if (modal.id === 'media-manager-modal') {
                    mediaManager.reset();
                }
            },
            awaitCloseAnimation: true,
            awaitOpenAnimation: true,
            debugMode: false
        });
    }
    initMicroModal();

    // navbar search
    ATE.searchKey = '';

    const debouncedSearch = debounce(getNavbarSearchData, 200);

    $('.navbar-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedSearch()
    }));

    // get Navbar Search Data
    function getNavbarSearchData() {
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route(routePrefix() . '.getNavbarSearchData') }}',
            data: {
                search: ATE.searchKey,
            },
            success: function(data) {
                $('.navbar-search').html(data);
            }
        });
    }

    // initSellerPayoutModal
    function initSellerPayoutModal() {
        $("[name='payment_method'").on('change', function() {
            if ($(this).val() == "cash") {
                $('.payment-details').addClass('hidden');
            } else {
                $('.payment-details').removeClass('hidden');
            }
        })
        $('.seller-payout-modal').on('click', function() {
            let id = $(this).data('id');
            let shopId = $(this).data('shop-id');
            let due = $(this).data('due');
            let demandedAmount = $(this).data('demanded-amount');
            let bankName = $(this).data('bank-name');
            let bankAccName = $(this).data('bank-acc-name');
            let bankAccNo = $(this).data('bank-acc-no');
            let bankRoutingNo = $(this).data('bank-routing-no');
            let cash = $(this).data('cash');
            let bank = $(this).data('bank');

            $('.due').html(due ? due : 0);
            $('.demanded').html(demandedAmount ? demandedAmount : 0);
            $("[name='amount'").val(demandedAmount ? demandedAmount : '');
            $("[name='id'").val(id ? id : null);

            $("[name='shop_id'").val(shopId);

            if (bank) {
                $('.bank').removeClass('hidden');
                $('.bank-details').removeClass('hidden');
                $('.bank-name').html(bankName);
                $('.bank-acc-name').html(bankAccName);
                $('.bank-acc-no').html(bankAccNo);
                $('.bank-routing-no').html(bankRoutingNo);
            } else {
                $('.bank').addClass('hidden');
                $('.bank-details').addClass('hidden');
            }

            if (cash) {
                $('.cash').removeClass('hidden');
            } else {
                $('.cash').addClass('hidden');
            }

        })
    }
    initSellerPayoutModal();

    // initRefundPaymentModal
    function initRefundPaymentModal() {

        $('.refund-payment-modal').on('click', function() {
            let id = $(this).data('id');
            let invoiceNumber = $(this).data('invoice');
            let totalAmount = $(this).data('order-amount');



            $('#invoice-number').text(invoiceNumber);
            $('#total-amount').text(totalAmount);
            $("[name='id'").val(id);



        });
    }
    initRefundPaymentModal();

    // handle openAI content generation
    function generateOpenAIContent(subject) {
        let title = $('input[name="name"]').val()

        if (!title) {
            title = $('input[name="title"]').val()
        }

        let prompt = 'Generate ' + subject + ' for ' + title

        const regex = new RegExp(`\/products\/`);
        if (subject == 'description' && location.pathname.match(regex)) {
            prompt = 'Generate ' + subject + ' for e-commerce website for the product' + title +
                ' including an introduction followed by some key features'
        }

        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                prompt: prompt
            },
            url: '{{ route('openai') }}',
            success: function(data) {
                if (subject == 'description' || subject == 'content') {
                    const editor = new Jodit('.jodit-editor')
                    editor.setEditorValue(data);
                } else {
                    $(`[name=${subject}]`).val(data)
                }
            }
        });
    }

    function syncMainBannerBackgroundFields($bannerItem) {
        var useBackgroundVideo = $bannerItem.find('.main-banner-background-toggle').is(':checked');
        $bannerItem.find('.main-banner-background-image-field').toggleClass('hidden', useBackgroundVideo);
        $bannerItem.find('.main-banner-background-video-field').toggleClass('hidden', !useBackgroundVideo);
    }

    function initMainBannerBackgroundToggle(scope) {
        var $scope = scope ? $(scope) : $(document);
        $scope.find('.main-banner-item').each(function() {
            syncMainBannerBackgroundFields($(this));
        });
    }

    // add more - using event delegation for dynamically loaded content
    $(document).on('click', '[data-toggle="add-more"]', function(e) {
        e.preventDefault();
        var $this = $(this);
        var content = $this.data("content");
        var target = $this.data("target");
        var index = $(target).find('.main-banner-item').length;
        var newContent = content.replace(/__INDEX__/g, index);
        $(target).append(newContent);
        initSelect2();
        initMicroModal();
        mediaManager.removeInputValue();
        initMainBannerBackgroundToggle(target);
    });

    // remove banner item
    $(document).on('click', '.remove-banner-item', function(e) {
        e.preventDefault();
        $(this).closest('.main-banner-item').remove();
    });

    $(document).on('change', '.main-banner-background-toggle', function() {
        syncMainBannerBackgroundFields($(this).closest('.main-banner-item'));
    });
    initMainBannerBackgroundToggle();

    // remove parent
    $(document).on(
        "click",
        '[data-toggle="remove-parent"]',
        function() {
            var $this = $(this);
            var parent = $this.data("parent");
            $this.closest(parent).remove();
        }
    );

    // homepage-form
    function initHomepageFormSubmit() {
        $('.homepage-form').each(function() {
            $(this).on('submit', function(e) {
                e.preventDefault();

                let $this = this;
                $($this).find('.submit-btn').attr('disabled', 'disabled');
                $($this).find('.submit-btn').html('{{ translate('Please Wait..') }}');

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: $($this).serialize(),
                    url: '{{ route('admin.general-settings.update') }}',
                    success: function(data) {
                        if (data.success) {
                            notifyMe('success',
                                '{{ translate('Information updated successfully.') }}');
                            $($this).find('.submit-btn').removeAttr('disabled');
                            $($this).find('.submit-btn').html(
                                '{{ translate('Save Changes') }}');

                            // Update iframe source
                            $('#homepage-iframe').attr('src', '{{ url('/') }}');
                        } else {
                            notifyMe('error', '{{ translate('Something went wrong') }}');
                        }
                    }
                });
            })
        })
    }
    initHomepageFormSubmit()

    // roles permissions

    // check full access checkbox if all are checked under this
    function initPermissionFullAccessCheck() {
        $('.permission-group-tr').each(function() {
            let checkedLength = $(this).find('.permissions-selection').find('input[type="checkbox"]:checked')
                .length;
            let allLength = $(this).find('.permissions-selection').find('input[type="checkbox"]').length;
            if (checkedLength == allLength) {
                $(this).find('input[name="full_access"]').prop('checked', 'true');
            } else {
                $(this).find('input[name="full_access"]').prop('checked', false);
            }
        })
    }
    initPermissionFullAccessCheck();

    // toggle Group all
    function toggleGroupAll($this) {
        $($this).closest('.permission-group-tr').find("input:checkbox").prop('checked', $this.checked);
    }

    // Delete quick link functionality
    function initQuickLinkDelete() {
        $(document).on('click', '.delete-quick-link', function(e) {
            e.preventDefault();

            let $button = $(this);
            let $quickLinkInput = $button.closest('.quick-link-input');
            let index = $button.data('index');

            // If no index attribute, it's a newly added item, just remove it from DOM
            if (index === undefined) {
                // Use MicroModal for confirmation
                showDeleteQuickLinkConfirmation($quickLinkInput, $button, null);
                return;
            }

            // Use MicroModal for confirmation
            showDeleteQuickLinkConfirmation($quickLinkInput, $button, index);
        });

        function showDeleteQuickLinkConfirmation($quickLinkInput, $button, index) {
            // Set up modal content
            $('#confirm-modal-title').html('{{ translate('Delete Quick Link') }}');
            $('#confirm-modal-text').html('{{ translate('Are you sure you want to delete this quick link?') }}');

            let $submitBtn = $('#confirm-modal-submit');
            $submitBtn.text('{{ translate('Delete') }}');
            $submitBtn.removeClass().addClass('modal__btn modal__btn-success bg-red-500 leading-none text-white px-10 py-2.5 rounded uppercase');

            // Remove form attributes to prevent default form submission
            $('#confirm-modal form').removeAttr('action');
            $('#confirm-modal-method').val('');

            // Store the handler function for submit
            $submitBtn.off('click').on('click', function(e) {
                e.preventDefault();

                MicroModal.close('confirm-modal');

                if (index === null) {
                    // New item - just remove from DOM
                    $quickLinkInput.fadeOut(300, function() {
                        $(this).remove();
                        notifyMe('success', '{{ translate('Quick link deleted successfully') }}');
                    });
                } else {
                    // Existing item - send AJAX request
                    performQuickLinkDelete($quickLinkInput, $button, index);
                }
            });

            // Show the modal
            MicroModal.show('confirm-modal');
        }

        function performQuickLinkDelete($quickLinkInput, $button, index) {
            // Show loading state
            $button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            // Send AJAX request to delete the quick link
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: '{{ route('admin.quick-links.delete', ':index') }}'.replace(':index', index),
                success: function(response) {
                    if (response.success) {
                        $quickLinkInput.fadeOut(300, function() {
                            $(this).remove();
                            // Re-index remaining quick links
                            $('.quick-link-input').each(function(i) {
                                $(this).attr('data-index', i);
                                $(this).find('.delete-quick-link').attr('data-index', i);
                            });
                        });
                        notifyMe('success', response.message);
                    } else {
                        notifyMe('error', response.message || '{{ translate('Error deleting quick link') }}');
                        $button.prop('disabled', false).html('<i class="fa-solid fa-trash"></i>');
                    }
                },
                error: function() {
                    notifyMe('error', '{{ translate('Error deleting quick link') }}');
                    $button.prop('disabled', false).html('<i class="fa-solid fa-trash"></i>');
                }
            });
        }
    }
    initQuickLinkDelete();
</script>

{{-- customer segment CRUD --}}
<script>
    $(document).on('click', '.addCustomerSegment', function() { 
        let $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {},
            url: '{{ route('admin.customers.storeCustomerSegment') }}',
            success: function(data) {
                if (data.success) {
                    notifyMe('success', '{{ translate('Segment added successfully.') }}');
                    $('.customerSegmentViews').empty().html(data.segmentViews);
                } else {
                    notifyMe('error', '{{ translate('Something went wrong') }}');
                }
                $btn.prop('disabled', false);

            }
        });
    });

    $(document).on('click', '.removeSegment', function() { 
        let $btn = $(this);
        $btn.prop('disabled', true);

        let id = $(this).data('segment-id');
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                id:id
            },
            url: '{{ route('admin.customers.removeCustomerSegment') }}',
            success: function(data) {
                if (data.success) {
                    notifyMe('success', '{{ translate('Segment removed successfully.') }}');
                    $('.customerSegmentViews').empty().html(data.segmentViews);
                } else {
                    notifyMe('error', '{{ translate('Something went wrong') }}');
                }
                $btn.prop('disabled', false);

            }
        });
    });

    $(document).on('focusout', '.input', function() { 
        let $input = $(this); 
        let id     = $input.data('segment-id');
        let name   = $input.attr("name");
        let value  = $input.val();
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                id   : id,
                name : name,
                value: value
            },
            url: '{{ route('admin.customers.updateCustomerSegment') }}',
            success: function(data) {
                if (data.success) {
                    notifyMe('success', '{{ translate('Segment updated successfully.') }}');
                    $('.customerSegmentViews').empty().html(data.segmentViews);
                } else {
                    notifyMe('error', '{{ translate('Something went wrong') }}');
                }

            }
        });
    });
</script>
{{-- customer segment CRUD ends--}}


@vite('resources/backend/js/main.js')
