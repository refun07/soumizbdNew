<script>
    "use strict";

    ATE.searchKey = '';

    // submit form
    $('.return-refund-form').on('submit', function(e) {
        e.preventDefault();

        let $form = $(this);

        // Get all checked product variation checkboxes
        const selectedProductIds = $form.find('.return-product-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        // If none selected, show error
        if (selectedProductIds.length < 1) {
            notifyMe('error', '{{ translate('Please select at least one product') }}');
            return;
        }

        // Prepare form data for submission (including file uploads)
        let formData = new FormData(this);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                // Optional: show loader or disable submit button
            },
            success: function(response) {
                if (response.status === 200) {
                    notifyMe('success', response.message ||
                        '{{ translate('Return request submitted successfully') }}');
                    window.location.href = "{{ route('admin.return-requests.index') }}";
                } else {
                    notifyMe('error', response.message ||
                        '{{ translate('Something went wrong') }}');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || '{{ translate('Something went wrong') }}';
                notifyMe('error', msg);
            },
        });
    });

    // show search result ui
    $('.return-refund-search-input').on('keyup', function(e) {
        const value = $(this).val().trim();
        debouncedOrderSearch();
        $(".return-refund-search").show();

        if (value === '' && e.key === 'Enter') {
            handleOrderClick(this);
        }
    });

    // search products
    const debouncedOrderSearch = debounce(getOrderInvoice, 300);
    $('.return-refund-search-input').on('input', ((e) => {
        ATE.searchKey = e.target.value
        debouncedOrderSearch()
    }));

    // get orders
    function getOrderInvoice() {


        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: '{{ route('return-refund.get-order-invoice') }}',
            data: {
                searchKey: ATE.searchKey,
            },
            success: function(data) {
                $('.return-refund-search-results').html(data);
            }
        });
    }

    // load More Products
    function loadMoreOrders($this, url) {
        $('.load-more-spin').removeClass('hidden');
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: url,
            success: function(data) {
                $($this).closest('div').remove();
                $(".return-refund-search-input input").focus();
                $('.return-refund-search-results').append(data);

            }
        });
    }

    // hadnle po product click
    function handleOrderClick($this) {

        let orderCode = $($this).data('code') || '';

        if (orderCode == '') {
            window.location = '{{ route(routePrefix() . '.return-requests.create') }}'
        } else {
            window.location = "{{ route(routePrefix() . '.return-requests.create') }}?search=" + orderCode;
        }


    }
</script>
